<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class ResetPasswordController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Show the OTP verification form.
     */
    public function showVerifyForm(Request $request): View
    {
        $email = $request->query('email');
        if (!$email) {
            return view('auth.forgot-password');
        }

        return view('auth.verify-otp', compact('email'));
    }

    /**
     * Verify the OTP and store verified email in session.
     */
    public function verifyOtp(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'otp'   => ['required', 'string', 'size:6'],
        ]);

        $user = User::where('email', $request->email)->first();

        try {
            $this->otpService->verify($user, 'password_reset', $request->otp);

            // Store in session that this email is verified for reset
            session(['password_reset_email' => $request->email]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'OTP verified successfully.', 
                    'redirect' => route('password.reset.form')
                ]);
            }

            return redirect()->route('password.reset.form')
                ->with('success', 'OTP verified successfully. You can now reset your password.');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => $e->getMessage()
                ], 422);
            }
            return back()->withInput($request->only('email'))
                ->withErrors(['otp' => $e->getMessage()]);
        }
    }

    /**
     * Show the final password reset form.
     */
    public function showResetForm(): View
    {
        $email = session('password_reset_email');
        if (!$email) {
            return view('auth.forgot-password')->with('error', 'Session expired. Please request a new OTP.');
        }

        return view('auth.reset-password', compact('email'));
    }

    /**
     * Handle final password reset.
     */
    public function resetPassword(Request $request): RedirectResponse|JsonResponse
    {
        $email = session('password_reset_email');
        if (!$email) {
            return redirect()->route('password.request')->with('error', 'Session expired. Please request a new OTP.');
        }

        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::where('email', $email)->first();
        if (!$user) {
            return redirect()->route('password.request')->with('error', 'User not found.');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Clear session
        session()->forget('password_reset_email');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true, 
                'message' => 'Password reset successful.', 
                'redirect' => route('login')
            ]);
        }

        return redirect()->route('login')->with('success', 'Password reset successful. You can now log in.');
    }
}
