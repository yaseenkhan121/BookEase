<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Service;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Display role-based analytics.
     */
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $data = [];

        if ($user->isAdmin()) {
            $data = $this->getAdminAnalytics();
        } elseif ($user->isProvider()) {
            $data = $this->getProviderAnalytics($user);
        } else {
            $data = $this->getCustomerAnalytics($user);
        }

        return view('analytics.index', $data);
    }

    /**
     * Admin Analytics Data
     */
    private function getAdminAnalytics(): array
    {
        // Bookings last 7 days chart
        $days = collect(range(6, 0))->map(fn($i) => Carbon::now()->subDays($i)->format('Y-m-d'));
        $bookingCounts = Booking::where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date');

        $chartData = [
            'labels' => $days->map(fn($date) => Carbon::parse($date)->format('D'))->toArray(),
            'values' => $days->map(fn($date) => $bookingCounts->get($date) ?? 0)->toArray(),
        ];

        // Provider growth (last 7 days)
        $providerGrowthCounts = User::where('role', 'provider')
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date');

        $providerGrowthChart = [
            'labels' => $days->map(fn($date) => Carbon::parse($date)->format('D'))->toArray(),
            'values' => $days->map(fn($date) => $providerGrowthCounts->get($date) ?? 0)->toArray(),
        ];

        // Revenue Trend (last 7 days)
        $revenueCounts = Booking::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(price) as total'))
            ->groupBy('date')
            ->pluck('total', 'date');

        $revenueChart = [
            'labels' => $days->map(fn($date) => Carbon::parse($date)->format('D'))->toArray(),
            'values' => $days->map(fn($date) => (float)($revenueCounts->get($date) ?? 0))->toArray(),
        ];

        return [
            'role' => 'admin',
            'chartData' => $chartData,
            'providerGrowthChart' => $providerGrowthChart,
            'revenueChart' => $revenueChart,
        ];
    }

    /**
     * Provider Analytics Data
     */
    private function getProviderAnalytics(User $user): array
    {
        $provider = $user->providerProfile;
        
        if (!$provider) {
            return [
                'role' => 'provider',
                'chartData' => ['labels' => [], 'values' => []],
                'serviceChart' => ['labels' => [], 'values' => []],
                'earningsChart' => ['labels' => [], 'values' => []],
                'no_provider_profile' => true,
            ];
        }

        // Bookings over time (last 7 days)
        $days = collect(range(6, 0))->map(fn($i) => Carbon::now()->subDays($i)->format('Y-m-d'));
        $bookingCounts = Booking::where('provider_id', $provider->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date');

        $chartData = [
            'labels' => $days->map(fn($date) => Carbon::parse($date)->format('D'))->toArray(),
            'values' => $days->map(fn($date) => $bookingCounts->get($date) ?? 0)->toArray(),
        ];

        // Service performance (bookings per service)
        $servicePerformanceData = Service::where('provider_id', $provider->id)
            ->withCount('bookings')
            ->orderBy('bookings_count', 'desc')
            ->limit(5)
            ->get();

        $serviceChart = [
            'labels' => $servicePerformanceData->pluck('name')->toArray(),
            'values' => $servicePerformanceData->pluck('bookings_count')->toArray(),
        ];

        // Earnings Trend (last 7 days)
        $earningsCounts = Booking::where('provider_id', $provider->id)
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(price) as total'))
            ->groupBy('date')
            ->pluck('total', 'date');

        $earningsChart = [
            'labels' => $days->map(fn($date) => Carbon::parse($date)->format('D'))->toArray(),
            'values' => $days->map(fn($date) => (float)($earningsCounts->get($date) ?? 0))->toArray(),
        ];

        return [
            'role' => 'provider',
            'chartData' => $chartData,
            'serviceChart' => $serviceChart,
            'earningsChart' => $earningsChart,
        ];
    }

    /**
     * Customer Analytics Data
     */
    private function getCustomerAnalytics(User $user): array
    {
        // Booking activity (last 7 days)
        $days = collect(range(6, 0))->map(fn($i) => Carbon::now()->subDays($i)->format('Y-m-d'));
        $bookingCounts = Booking::where('customer_id', $user->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date');

        $chartData = [
            'labels' => $days->map(fn($date) => Carbon::parse($date)->format('D'))->toArray(),
            'values' => $days->map(fn($date) => $bookingCounts->get($date) ?? 0)->toArray(),
        ];

        return [
            'role' => 'customer',
            'chartData' => $chartData,
        ];
    }
}
