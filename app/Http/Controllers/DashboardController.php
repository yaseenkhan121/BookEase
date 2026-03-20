<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class DashboardController extends Controller
{
    /**
     * Dashboard Home: Role-based stats and analytics.
     */
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $query = Appointment::query();

        $stats = Cache::remember("dashboard_stats_{$user->id}_{$user->role}", now()->addMinutes(10), function() use ($user, &$query) {
            if ($user->isAdmin()) {
                // Consolidated appointment stats: 1 query instead of 5
                $appointmentAgg = Appointment::selectRaw("
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
                ")->first();

                return [
                    'total_appointments'   => $appointmentAgg->total,
                    'total_providers'      => Provider::count(),
                    'total_services'       => Service::count(),
                    'total_customers'      => User::where('role', 'customer')->count(),
                    'pending_requests'     => $appointmentAgg->pending,
                    'confirmed_bookings'   => $appointmentAgg->approved,
                    'completed_bookings'   => $appointmentAgg->completed,
                    'cancelled_bookings'   => $appointmentAgg->cancelled,
                    'running_appointments' => $appointmentAgg->approved,
                    'categories'           => Provider::select('business_category')
                        ->whereNotNull('business_category')
                        ->distinct()
                        ->pluck('business_category'),
                    'pending_providers'    => Provider::where('status', 'pending')->count(),
                    'total_users'          => User::count(),
                ];
            } elseif ($user->isProvider()) {
                $provider = $user->providerProfile;

                if (!$provider) {
                    return [
                        'total_appointments'   => 0,
                        'completed_projects'   => 0,
                        'running_appointments' => 0,
                        'pending_requests'     => 0,
                        'setup_required'       => true,
                    ];
                } else {
                    $query = Appointment::where('provider_id', $provider->id);
                    return [
                        'total_appointments'   => (clone $query)->count(),
                        'completed_projects'   => (clone $query)->where('status', 'completed')->count(),
                        'running_appointments' => (clone $query)->where('status', 'approved')->count(),
                        'pending_requests'     => (clone $query)->where('status', 'pending')->count(),
                        'average_rating'       => $provider->average_rating,
                        'total_reviews'        => $provider->total_reviews,
                    ];
                }
            } else {
                // Customer
                $query = Appointment::where('customer_id', $user->id);
                return [
                    'total_appointments'   => (clone $query)->count(),
                    'completed_projects'   => (clone $query)->where('status', 'completed')->count(),
                    'running_appointments' => (clone $query)->where('status', 'approved')->count(),
                    'pending_requests'     => (clone $query)->where('status', 'pending')->count(),
                ];
            }
        });

        $upcoming = (clone $query)
            ->with(['service', 'customer', 'provider'])
            ->where('start_time', '>=', now())
            ->whereIn('status', ['approved', 'pending'])
            ->orderBy('start_time', 'asc')
            ->limit(5)
            ->get();

        // 7-Day Activity Chart
        $days = collect(range(6, 0))->map(fn($i) => Carbon::now()->subDays($i)->format('D'));

        $counts = (clone $query)
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as aggregate'))
            ->groupBy('date')
            ->get()
            ->pluck('aggregate', 'date');

        $chartData = [
            'labels' => $days->toArray(),
            'values' => collect(range(6, 0))->map(function ($i) use ($counts) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                return $counts->get($date) ?? 0;
            })->toArray(),
        ];

        $recentReviews = [];
        if ($user->isProvider() && $user->providerProfile) {
            $recentReviews = $user->providerProfile->reviews()
                ->with('customer')
                ->latest()
                ->limit(5)
                ->get();
        }

        return view('dashboard.index', compact('stats', 'upcoming', 'chartData', 'recentReviews'));
    }

    /**
     * Analytics View
     */
    public function analytics(): View
    {
        return $this->index();
    }

    /**
     * Reports View
     */
    public function reports(): View
    {
        return $this->index();
    }

    /**
     * Settings Page Redirect
     */
    public function settings()
    {
        return redirect()->route('settings.profile');
    }

    /**
     * Provider: Waiting for Approval View
     */
    public function waitingApproval(): View
    {
        return view('auth.pending-approval');
    }

    /**
     * Global Search: Context-aware, multi-entity searching.
     */
    public function search(Request $request): View
    {
        $searchTerm = $request->input('query');
        $user = Auth::user();

        if (!$searchTerm) {
            return view('search.index', ['appointments' => collect(), 'services' => collect(), 'providers' => collect(), 'query' => '']);
        }

        // 1. Appointments Search (Restricted by Role)
        $appointmentsQuery = Appointment::with(['service', 'provider', 'customer']);
        if ($user->role === 'provider') {
            $appointmentsQuery->where('provider_id', $user->providerProfile->id);
        } elseif ($user->role === 'customer') {
            $appointmentsQuery->where('customer_id', $user->id);
        }
        /** @var \Illuminate\Database\Query\Builder $appointmentsQuery */
        $appointments = $appointmentsQuery->where(function ($q) use ($searchTerm) {
            $q->where('id', 'LIKE', "%{$searchTerm}%")
              ->orWhereHas('customer', function($query) use ($searchTerm) {
                  $query->where('name', 'LIKE', "%{$searchTerm}%");
              })
              ->orWhereHas('service', function ($sq) use ($searchTerm) {
                  $sq->where('service_name', 'LIKE', "%{$searchTerm}%");
              });
        })->latest('start_time')->limit(10)->get();

        // 2. Services Search (Only active services)
        $services = Service::where('status', true)
            ->where(function (Builder $q) use ($searchTerm) {
                $q->where('service_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            })->latest()->limit(10)->get();

        // 3. Providers Search (Approved only)
        $providers = Provider::approved()
            ->where(function (Builder $q) use ($searchTerm) {
                $q->where('owner_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('business_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('specialization', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('city', 'LIKE', "%{$searchTerm}%");
            })->latest()->limit(10)->get();

        $query = $searchTerm;

        return view('search.index', compact('appointments', 'services', 'providers', 'query'));
    }
}