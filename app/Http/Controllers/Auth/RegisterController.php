<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Events\ProviderRegistered;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function show(): View
    {
        return view('auth.register');
    }

    /**
     * Handle account creation for both Customers and Providers.
     */
    public function register(Request $request): RedirectResponse
    {
        // 1. Validation
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(6)->letters()->numbers()],
            'role'     => ['required', 'in:customer,provider'],
        ], [
            'role.in' => 'Registration as an administrator is not permitted.',
        ]);

        try {
            // 2. Database Transaction
            $user = DB::transaction(function () use ($request) {
                
                $user = User::create([
                    'name'     => trim($request->name),
                    'email'    => strtolower(trim($request->email)),
                    'password' => Hash::make($request->password), 
                    'role'     => $request->role,
                    'status'   => $request->role === 'provider' ? 'pending' : 'active',
                    'email_verified_at' => now(),
                ]);

                // 3. Conditional Provider Setup
                if ($request->role === 'provider') {
                    Provider::create([
                        'user_id'         => $user->id,
                        'owner_name'      => $user->name,
                        'email'           => $user->email,
                        'phone'           => '', 
                        'business_name'   => $user->name . "'s Business",
                        'business_category' => 'Consultant',
                        'specialization'  => '', 
                        'bio'             => '',
                        'status'          => 'pending',
                        'setup_completed' => false,
                        'is_demo'          => false,
                    ]);
                    
                    event(new ProviderRegistered($user));
                }
                return $user;
            });

            // Trigger Laravel's standard verification mailer safely
            try {
                event(new Registered($user));
            } catch (\Exception $mailException) {
                \Illuminate\Support\Facades\Log::error('Registration Email Failed: ' . $mailException->getMessage());
                // Continue registration process even if email fails
            }

            // 4. Redirect to Login (No Auto-Login)
            $message = ($user->role === 'provider') 
                ? 'Your account has been created. Admin approval is required before you can sign in to your dashboard.'
                : 'Account created successfully. Please sign in to continue to your dashboard.';

            return redirect()->route('login')->with('success', $message);
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }
}