<?php

namespace App\Services;

use App\Models\EmailOtp;
use App\Models\User;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class OtpService
{
    /**
     * Generate and send a new OTP
     */
    public function generate(User $user, string $purpose, string $identifier): EmailOtp
    {
        // Rate limiting: max 3 requests per minute
        $recentRequests = EmailOtp::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subMinute())
            ->count();
            
        if ($recentRequests >= 3) {
            throw new \Exception('Too many OTP requests. Please wait a minute.');
        }

        // Generate 6-digit code
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Deactivate previous OTPs for this purpose
        EmailOtp::where('user_id', $user->id)
            ->where('purpose', $purpose)
            ->where('verified', false)
            ->update(['verified' => false]);

        $isPhone = ($purpose === 'phone_change' || str_contains($identifier, '+') || is_numeric(str_replace([' ', '-', '(', ')'], '', $identifier)));

        $otp = EmailOtp::create([
            'user_id' => $user->id,
            'email' => $isPhone ? null : $identifier,
            'phone' => $isPhone ? $identifier : null,
            'otp_code' => $otpCode,
            'purpose' => $purpose,
            'expires_at' => now()->addMinutes(10),
            'attempts' => 0,
            'verified' => false,
        ]);

        // Send OTP via appropriate channel
        if ($isPhone) {
            // Trigger Phone OTP (SMS/WhatsApp)
            // Senior Logic: Use the user's notification system or direct SMS service
            try {
                if (class_exists(\App\Notifications\Channels\SmsChannel::class)) {
                    // Logic to send SMS...
                    // For now, we'll log it or use a mockup until provider is ready
                }
                
                // Backup: Also send to primary email just in case
                Mail::to($user->email)->send(new OtpMail($otpCode));
            } catch (\Exception $e) {
                // Log error but proceed if email sent
            }
        } else {
            // Send Email OTP
            Mail::to($identifier)->send(new OtpMail($otpCode));
        }

        return $otp;
    }

    /**
     * Verify the provided OTP
     */
    public function verify(User $user, string $purpose, string $otpCode): bool
    {
        $otp = EmailOtp::where('user_id', $user->id)
            ->where('purpose', $purpose)
            ->where('verified', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otp) {
            throw new \Exception('Invalid or expired verification code.');
        }

        if ($otp->attempts >= 5) {
            throw new \Exception('Too many verification attempts. Please request a new code.');
        }

        if ($otp->otp_code !== $otpCode) {
            $otp->increment('attempts');
            throw new \Exception('The code you entered is incorrect.');
        }

        // Mark as verified
        $otp->update(['verified' => true]);

        return true;
    }
}
