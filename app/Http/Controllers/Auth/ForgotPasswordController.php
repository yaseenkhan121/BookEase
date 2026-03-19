<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    /**
     * Display the form to request a password reset link.
     */
    public function showLinkRequestForm(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Send a reset code to the given user.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        try {
            // Use existing OtpService to generate and send code
            app(\App\Services\OtpService::class)->generate($user, 'password_reset', $user->email);

            return redirect()->route('password.otp.verify', ['email' => $user->email])
                ->with('status', 'A 6-digit reset code has been sent to your email.');
        } catch (\Exception $e) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => $e->getMessage()]);
        }
    }
}
