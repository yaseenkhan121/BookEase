<?php

namespace App\Http\Controllers\Auth\Provider; // Matches your folder structure

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    /**
     * Display list of services based on user role.
     * Admin: All services
     * Provider: Own services
     * Customer: Active services from active/setup-completed providers
     */
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();

        // 1. Customer Role (Marketplace View)
        if ($user->isCustomer()) {
            $services = Service::with('provider.user')
                ->where('status', 'active')
                ->whereHas('provider', function($q) {
                    $q->where('status', 'approved')
                      ->where('setup_completed', true);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(12);

            return view('services.browse', compact('services'));
        }

        // 2. Admin & Provider Roles (Management View)
        $query = Service::query();

        if ($user->isAdmin()) {
            $provider = null;
            $query->with('provider'); 
        } else {
            // Provider Role
            $provider = Provider::where('email', $user->email)->first();
            
            if (!$provider) {
                return redirect()->route('dashboard')
                    ->with('error', 'Please complete your provider profile setup first.');
            }
            
            $query->where('provider_id', $provider->id);
        }

        $services = $query->orderBy('status', 'asc')
            ->orderBy('service_name', 'asc')
            ->paginate(10);

        return view('services.index', compact('services', 'provider'));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create(): View
    {
        $providers = Auth::user()->isAdmin() ? Provider::all() : [];
        return view('services.create', compact('providers'));
    }

    /**
     * Step 2: Store a newly created service.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'service_name' => [
                'required', 
                'string', 
                'max:255',
                function ($attribute, $value, $fail) use ($request, $user) {
                    $providerId = $user->isAdmin() ? $request->provider_id : Provider::where('email', $user->email)->value('id');
                    if (Service::where('provider_id', $providerId)->where('service_name', $value)->exists()) {
                        $fail('This service name is already taken for the selected provider.');
                    }
                }
            ],
            'description'  => ['required', 'string', 'max:1000'],
            'price'        => ['required', 'numeric', 'min:0'],
            'duration'     => ['required', 'integer', 'min:5', 'max:480'],
            'status'       => ['required', 'in:active,inactive'],
            'provider_id'  => $user->isAdmin() ? ['required', 'exists:providers,id'] : ['nullable']
        ]);

        if (!$user->isAdmin()) {
            $provider = Provider::where('email', $user->email)->firstOrFail();
            $validated['provider_id'] = $provider->id;
        }

        // Convert status string to boolean (Migration 2026_03_18_214802)
        $validated['status'] = $validated['status'] === 'active';

        $service = Service::create($validated);
        
        $provider = Provider::find($service->provider_id);
        if ($provider) {
            $provider->checkSetupCompletion();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Service added successfully.', 'redirect' => route('services.index')]);
        }

        return redirect()->route('services.index')
            ->with('success', 'Service added successfully.');
    }

    public function edit(Service $service): View
    {
        $this->checkAccess($service);
        $providers = auth()->user()->isAdmin() ? Provider::all() : [];
        return view('services.edit', compact('service', 'providers'));
    }

    /**
     * Update the service details.
     */
    public function update(Request $request, Service $service): RedirectResponse|JsonResponse
    {
        $this->checkAccess($service);
        $user = Auth::user();

        $validated = $request->validate([
            'service_name' => [
                'required', 
                'string', 
                'max:255',
                function ($attribute, $value, $fail) use ($request, $user, $service) {
                    $providerId = $user->isAdmin() ? $request->provider_id : $service->provider_id;
                    if (Service::where('provider_id', $providerId)
                        ->where('service_name', $value)
                        ->where('id', '!=', $service->id)
                        ->exists()) {
                        $fail('This service name is already taken for the selected provider.');
                    }
                }
            ],
            'description'  => ['required', 'string', 'max:1000'],
            'price'        => ['required', 'numeric', 'min:0'],
            'duration'     => ['required', 'integer', 'min:5', 'max:480'],
            'status'       => ['required', 'in:active,inactive'],
            'provider_id'  => $user->isAdmin() ? ['required', 'exists:providers,id'] : ['nullable']
        ]);


        // Convert status string to boolean (Migration 2026_03_18_214802)
        $validated['status'] = $validated['status'] === 'active';

        $service->update($validated);

        $provider = Provider::find($service->provider_id);
        if ($provider) {
            $provider->checkSetupCompletion();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Service updated successfully.', 'redirect' => route('services.index')]);
        }

        return redirect()->route('services.index')
            ->with('success', 'Service updated successfully.');
    }

    /**
     * Step 2: Delete service with dependency check (Step 6 status flow).
     */
    public function destroy(Service $service): RedirectResponse
    {
        $this->checkAccess($service);

        // Check for active appointments to prevent database orphans
        $hasBookings = $service->appointments()
            ->whereIn('status', ['pending', 'approved', 'in_progress'])
            ->exists();

        if ($hasBookings) {
            return back()->with('error', 'Cannot delete service with active appointments. Set it to inactive instead.');
        }

        $service->delete();

        return redirect()->route('services.index')
            ->with('success', 'Service removed.');
    }

    /**
     * Helper: Rapid toggle service status between Active/Inactive.
     */
    public function toggleStatus(Service $service): JsonResponse|RedirectResponse
    {
        $this->checkAccess($service);
        
        // Toggle boolean status (1 -> 0, 0 -> 1)
        $newStatus = !$service->status;
        $service->update(['status' => $newStatus]);
        
        $provider = Provider::find($service->provider_id);
        if ($provider) {
            $provider->checkSetupCompletion();
        }

        $statusText = $newStatus ? 'Active' : 'Inactive';

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true, 
                'message' => "Service is now {$statusText}.",
                'status' => $newStatus,
                'status_text' => $statusText,
                'status_class' => $newStatus ? 'status-active' : 'status-inactive'
            ]);
        }

        return back()->with('success', "Service is now {$statusText}.");
    }

    /**
     * Enforce security by checking Provider ID or Admin role.
     */
    private function checkAccess(Service $service): void
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            return;
        }

        $provider = Provider::where('email', $user->email)->firstOrFail();
        
        if ($service->provider_id !== $provider->id) {
            abort(403, 'Unauthorized action.');
        }
    }
}