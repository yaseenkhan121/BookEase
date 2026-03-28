<?php

namespace App\Http\Controllers\Auth\Provider;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProviderProfileController extends Controller
{
    /**
     * Show the profile edit form for the authenticated provider.
     */
    public function edit(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $provider = $user->provider;

        if (!$provider) {
            // Create a stub provider if it doesn't exist (safety)
            $provider = Provider::create([
                'user_id' => $user->id,
                'owner_name' => $user->name,
                'email' => $user->email,
                'status' => Provider::STATUS_PENDING,
            ]);
        }

        $categories = Provider::CATEGORIES;

        return view('provider.profile.edit', compact('provider', 'categories'));
    }

    /**
     * Update the provider's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $provider = $user->provider;

        if (!$provider) {
            abort(404, 'Provider profile not found.');
        }

        $validated = $request->validate([
            'business_name'     => ['required', 'string', 'max:255'],
            'business_category' => ['required', 'string', 'in:' . implode(',', Provider::CATEGORIES)],
            'specialization'    => ['required', 'string', 'max:255'],
            'phone'             => ['required', 'string', 'max:20'],
            'address'           => ['required', 'string', 'max:255'],
            'city'              => ['required', 'string', 'max:100'],
            'country'           => ['required', 'string', 'max:100'],
            'bio'               => ['nullable', 'string', 'max:2000'],
            'profile_image'     => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
        ]);

        // Handle Image Upload
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($provider->profile_image && Storage::disk('public')->exists($provider->profile_image)) {
                Storage::disk('public')->delete($provider->profile_image);
            }

            $path = $request->file('profile_image')->store('profile_images', 'public');
            $validated['profile_image'] = $path;
            
            // Sync with User table for consistency
            $user->update(['profile_image' => $path]);
        }

        $provider->update($validated);
        
        // Re-check setup completion
        $provider->checkSetupCompletion();

        return back()->with('success', 'Your profile has been updated successfully.');
    }
}
