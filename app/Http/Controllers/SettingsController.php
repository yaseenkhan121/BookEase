<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\UserSetting;
use App\Models\Provider;
use App\Models\User;
use App\Events\ProfileUpdated;
use App\Events\ProfileImageUpdated;

class SettingsController extends Controller
{
    /**
     * Show Profile Settings
     */
    public function profile()
    {
        return view('settings.profile');
    }

    /**
     * Show Security Settings
     */
    public function security()
    {
        return view('settings.security');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $oldEmail = $user->email;

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ];

        if ($user->role === 'provider') {
            $rules = array_merge($rules, [
                'business_name' => 'nullable|string|max:255',
                'specialization' => 'nullable|string|max:255',
                'bio' => 'nullable|string|max:2000',
                'address' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
            ]);
        }

        $validated = $request->validate($rules);

        // Check if sensitive identifiers (email or phone) are changing
        $emailChanging = ($validated['email'] !== $user->email);
        $phoneChanging = ($validated['phone'] && $validated['phone'] !== $user->phone_number);

        if ($emailChanging || $phoneChanging) {
            try {
                $purpose = $emailChanging ? 'email_change' : 'phone_change';
                $newVal = $emailChanging ? $validated['email'] : $validated['phone'];
                
                $otpService = app(\App\Services\OtpService::class);
                $otpService->generate($user, $purpose, $newVal);
                
                session([
                    'pending_email_change' => $emailChanging ? $validated['email'] : null,
                    'pending_phone_change' => $phoneChanging ? $validated['phone'] : null,
                    'pending_profile_data' => $request->except(['_token', 'email', 'phone', 'profile_image']),
                    'otp_purpose' => $purpose
                ]);

                if ($request->hasFile('profile_image')) {
                    $path = $request->file('profile_image')->store('temp', 'public');
                    session(['pending_profile_image' => $path]);
                }

                $message = $emailChanging 
                    ? 'A verification code has been sent to your new email address.' 
                    : 'A verification code has been sent to your new phone number via SMS/WhatsApp.';

                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'redirect' => route('settings.otp.verify'),
                        'message' => $message
                    ]);
                }

                return redirect()->route('settings.otp.verify')->with('success', $message);
            } catch (\Exception $e) {
                return back()->withErrors([$emailChanging ? 'email' : 'phone' => $e->getMessage()]);
            }
        }

        // If email NOT changing, update profile directly
        DB::transaction(function () use ($user, $validated, $request, $oldEmail) {
            $user->name = $validated['name'];
            $user->phone_number = $validated['phone'];

            if ($request->hasFile('profile_image')) {
                if ($user->profile_image && !str_starts_with($user->profile_image, 'http') && Storage::disk('public')->exists($user->profile_image)) {
                    Storage::disk('public')->delete($user->profile_image);
                }
                $path = $request->file('profile_image')->store('profile_images', 'public');
                $user->profile_image = $path;
                ProfileImageUpdated::dispatch($user);
            }

            $user->save();

            if ($user->role === 'provider') {
                $provider = Provider::where('user_id', $user->id)->first();
                if ($provider) {
                    $provider->update([
                        'owner_name' => $user->name,
                        'phone' => $user->phone_number,
                        'business_name' => $request->business_name ?? $provider->business_name,
                        'specialization' => $request->specialization ?? $provider->specialization,
                        'bio' => $request->bio ?? $provider->bio,
                        'address' => $request->address ?? $provider->address,
                        'city' => $request->city ?? $provider->city,
                        'country' => $request->country ?? $provider->country,
                        'profile_image' => $user->profile_image,
                    ]);
                }
            }

            ProfileUpdated::dispatch($user);
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Profile updated successfully.']);
        }

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        try {
            $otpService = app(\App\Services\OtpService::class);
            $otpService->generate($user, 'password_change', $user->email);
            
            session([
                'pending_password_change' => Hash::make($request->new_password),
                'otp_purpose' => 'password_change'
            ]);

            return redirect()->route('settings.otp.verify')->with('success', 'A verification code has been sent to your registered email address.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show OTP verification screen
     */
    public function showOtpVerify()
    {
        if (!session()->has('otp_purpose')) {
            return redirect()->route('settings.profile');
        }

        return view('settings.otp-verify');
    }

    /**
     * Process OTP verification
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6',
        ]);

        $user = auth()->user();
        $purpose = session('otp_purpose');
        $otpService = app(\App\Services\OtpService::class);

        try {
            $otpService->verify($user, $purpose, $request->otp_code);

            if ($purpose === 'email_change') {
                $this->finalizeEmailChange($user);
            } elseif ($purpose === 'phone_change') {
                $this->finalizePhoneChange($user);
            } elseif ($purpose === 'password_change') {
                $this->finalizePasswordChange($user);
            }

            session()->forget(['otp_purpose', 'pending_email_change', 'pending_profile_data', 'pending_profile_image', 'pending_password_change']);

            return redirect()->route($purpose === 'email_change' ? 'settings.profile' : 'settings.security')
                ->with('success', 'Verification successful. Your account has been updated.');

        } catch (\Exception $e) {
            return back()->withErrors(['otp_code' => $e->getMessage()]);
        }
    }

    private function finalizeEmailChange($user)
    {
        $newEmail = session('pending_email_change');
        $profileData = session('pending_profile_data');
        $profileImage = session('pending_profile_image');

        DB::transaction(function () use ($user, $newEmail, $profileData, $profileImage) {
            $user->email = $newEmail;
            $user->name = $profileData['name'] ?? $user->name;
            $user->phone_number = $profileData['phone'] ?? $user->phone_number;

            if ($profileImage) {
                // Move from temp to profile_images
                $newPath = 'profile_images/' . basename($profileImage);
                Storage::disk('public')->move($profileImage, $newPath);
                $user->profile_image = $newPath;
                ProfileImageUpdated::dispatch($user);
            }

            $user->save();

            if ($user->role === 'provider') {
                $provider = Provider::where('user_id', $user->id)->first();
                if ($provider) {
                    $provider->update([
                        'email' => $user->email,
                        'owner_name' => $user->name,
                        'phone' => $user->phone_number,
                        'business_name' => $profileData['business_name'] ?? $provider->business_name,
                        'specialization' => $profileData['specialization'] ?? $provider->specialization,
                        'bio' => $profileData['bio'] ?? $provider->bio,
                        'address' => $profileData['address'] ?? $provider->address,
                        'city' => $profileData['city'] ?? $provider->city,
                        'country' => $profileData['country'] ?? $provider->country,
                        'profile_image' => $user->profile_image,
                    ]);
                }
            }

            ProfileUpdated::dispatch($user);
        });
    }

    private function finalizePhoneChange($user)
    {
        $newPhone = session('pending_phone_change');
        $profileData = session('pending_profile_data');
        $profileImage = session('pending_profile_image');

        DB::transaction(function () use ($user, $newPhone, $profileData, $profileImage) {
            $user->phone_number = $newPhone;
            $user->name = $profileData['name'] ?? $user->name;

            if ($profileImage) {
                $newPath = 'profile_images/' . basename($profileImage);
                Storage::disk('public')->move($profileImage, $newPath);
                $user->profile_image = $newPath;
                ProfileImageUpdated::dispatch($user);
            }

            $user->save();

            if ($user->role === 'provider') {
                $provider = Provider::where('user_id', $user->id)->first();
                if ($provider) {
                    $provider->update([
                        'phone' => $user->phone_number,
                        'owner_name' => $user->name,
                        'business_name' => $profileData['business_name'] ?? $provider->business_name,
                        'specialization' => $profileData['specialization'] ?? $provider->specialization,
                        'bio' => $profileData['bio'] ?? $provider->bio,
                        'address' => $profileData['address'] ?? $provider->address,
                        'city' => $profileData['city'] ?? $provider->city,
                        'country' => $profileData['country'] ?? $provider->country,
                        'profile_image' => $user->profile_image,
                    ]);
                }
            }

            ProfileUpdated::dispatch($user);
        });
    }

    private function finalizePasswordChange($user)
    {
        $user->password = session('pending_password_change');
        $user->save();
    }

    public function resendOtp()
    {
        $user = auth()->user();
        $purpose = session('otp_purpose');
        
        $email = null;
        if ($purpose === 'email_change') {
            $email = session('pending_email_change');
        } elseif ($purpose === 'phone_change') {
            $email = session('pending_phone_change'); // Passing phone to identifier
        } else {
            $email = $user->email;
        }

        if (!$purpose || !$email) {
            return redirect()->route('settings.profile');
        }

        try {
            $otpService = app(\App\Services\OtpService::class);
            $otpService->generate($user, $purpose, $email);
            return back()->with('success', 'A new verification code has been sent.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }


    /**
     * Delete Account securely
     */
    public function deleteAccount(Request $request)
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return back()->withErrors(['error' => 'Admin accounts cannot be deleted.']);
        }

        $request->validate([
            'password_confirmation' => 'required',
        ]);

        if (!Hash::check($request->password_confirmation, $user->password)) {
            return back()->withErrors(['password_confirmation' => 'Incorrect password. Account deletion failed.']);
        }

        DB::transaction(function () use ($user) {
            if ($user->role === 'provider') {
                $provider = Provider::where('email', $user->email)->first();
                if ($provider) {
                    \App\Models\Service::where('provider_id', $provider->id)->delete();
                    \App\Models\Availability::where('provider_id', $provider->id)->delete();
                    \App\Models\Appointment::where('provider_id', $provider->id)->delete();
                    $provider->delete();
                }
            } elseif ($user->role === 'customer') {
                \App\Models\Appointment::where('customer_id', $user->id)->delete();
            }

            UserSetting::where('user_id', $user->id)->delete();
            
            if ($user->profile_image && !str_starts_with($user->profile_image, 'http') && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $user->delete();
        });

        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Your account and all associated data have been permanently deleted.');
    }

    /**
     * Show Notifications Settings
     */
    public function notifications()
    {
        $settings = UserSetting::firstOrCreate(
            ['user_id' => auth()->id()],
            [
                'email_notifications' => true,
                'booking_notifications' => true,
                'reminder_notifications' => true,
            ]
        );
        return view('settings.notifications', compact('settings'));
    }

    public function updateNotifications(Request $request)
    {
        $settings = UserSetting::where('user_id', auth()->id())->first();
        if ($settings) {
            $settings->update([
                'email_notifications' => $request->has('email_notifications'),
                'booking_notifications' => $request->has('booking_notifications'),
                'reminder_notifications' => $request->has('reminder_notifications'),
            ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Notification preferences saved.']);
        }

        return back()->with('success', 'Notification preferences saved.');
    }

    /**
     * Show Appearance Settings
     */
    public function appearance()
    {
        return view('settings.appearance');
    }

    public function updateAppearance(Request $request)
    {
        $validated = $request->validate([
            'theme_preference' => 'required|in:light,dark',
        ]);

        $user = auth()->user();
        $user->theme_preference = $validated['theme_preference'];
        $user->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'theme' => $user->theme_preference]);
        }

        return back()->with('success', 'Appearance updated successfully.');
    }

    /**
     * Redirects for replaced sections
     */
    public function connections()
    {
        return redirect()->route('settings.profile');
    }

    public function provider()
    {
        return redirect()->route('settings.profile');
    }

    public function system()
    {
        return redirect()->route('dashboard');
    }
}
