<?php

namespace App\Http\Controllers\Auth\Provider;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Builder;

class ProviderController extends Controller
{
    /**
     * Display a listing of providers.
     * Loads ALL approved providers by default. Supports optional search filtering.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $query = Provider::approved()
            ->whereHas('services', function ($q) {
                $q->where('status', 1); // boolean: only active services
            })
            ->with(['user', 'services' => function ($q) {
                $q->where('status', 1);
            }])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating');

        // Optional search filter - only applies when a keyword is provided
        if ($search) {
            /** @var \Illuminate\Database\Query\Builder $query */
            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'LIKE', "%{$search}%")
                  ->orWhere('owner_name', 'LIKE', "%{$search}%")
                  ->orWhere('specialization','LIKE', "%{$search}%")
                  ->orWhere('city',          'LIKE', "%{$search}%")
                  ->orWhereHas('services', function ($sq) use ($search) {
                      $sq->where('status', 1)
                   ->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $providers = $query->latest()->paginate(12);

        return view('providers.index', compact('providers', 'search'));
    }

    /**
     * Store or update a provider profile.
     */
    public function store(Request $request): RedirectResponse
    {
        // ... implementation remains same or updated via admin tools ...
        return back();
    }

    /**
     * Step 2: Fetch services for a specific provider (AJAX).
     */
    public function getServices(Provider $provider): JsonResponse
    {
        $services = $provider->services()
            ->where('status', 1)
            ->get(['id', 'name', 'price', 'duration_minutes']);

        return response()->json($services);
    }

    /**
     * Display the specified provider profile with services, reviews, and availability.
     */
    public function show(Provider $provider): View
    {
        $provider->load([
            'services' => fn($q) => $q->where('status', 1),
            'availabilities',
            'reviews.customer'
        ]);

        // Append counts for Quick Info Strip
        $provider->loadCount('reviews');

        return view('providers.show', compact('provider'));
    }

    /**
     * Remove a provider.
     */
    public function destroy(Provider $provider): RedirectResponse
    {
        if ($provider->profile_image) {
            Storage::disk('public')->delete($provider->profile_image);
        }

        $provider->delete();

        return back()->with('success', 'Provider removed from system.');
    }
}