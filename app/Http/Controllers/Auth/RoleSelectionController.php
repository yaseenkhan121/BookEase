<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Provider;

class RoleSelectionController extends Controller
{
    /**
     * Show role selection view.
     */
    public function show()
    {
        // Only allow if the user is authenticated and hasn't explicitly chosen a personalized role yet 
        // Or we can just rely on the redirect logic.
        return view('auth.google-role-selection');
    }

    /**
     * Update the user's role.
     */
    public function update(Request $request)
    {
        $request->validate([
            'role' => 'required|in:customer,provider',
        ]);

        $user = Auth::user();
        $user->update([
            'role'   => $request->role,
            'status' => 'active'
        ]);

        // If they chose provider, ensure a provider record is created (if not already)
        if ($request->role === 'provider' && !$user->providerProfile) {
            Provider::create([
                'user_id'           => $user->id,
                'owner_name'        => $user->name,
                'email'             => $user->email,
                'phone'             => $user->phone_number ?? '',
                'business_name'     => $user->name . "'s Business",
                'business_category' => 'Consultant',
                'specialization'    => '',
                'bio'               => '',
                'status'            => 'pending', // Reverted to pending for manual approval
                'setup_completed'   => false,
            ]);
        }

        if ($user->isProvider()) {
            return redirect('/dashboard')->with('success', 'Your account is ready!');
        }

        return redirect('/dashboard')->with('success', 'Your account is ready!');
    }
}
