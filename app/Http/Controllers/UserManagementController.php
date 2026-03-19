<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Provider;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Notifications\ProviderStatusNotification;
use Illuminate\Database\Eloquent\Builder;

class UserManagementController extends Controller
{
    /**
     * Display the users list with search, filter, and pagination.
     */
    public function index(Request $request): View
    {
        $query = User::query();

        // Search by name or email
        if ($search = $request->input('search')) {
            $query->whereNested(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        // Exclude the currently logged-in admin from the list
        $query->where('id', '!=', Auth::id());

        // Eager load provider relationship for provider users
        $query->with('providerProfile');

        $users = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Stats for the header cards
        $stats = [
            'total_users'     => User::where('id', '!=', Auth::id())->count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_providers' => User::where('role', 'provider')->count(),
            'new_this_month'  => User::where('created_at', '>=', now()->startOfMonth())->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Show a single user's details + activity.
     */
    public function show(User $user): View
    {
        $this->guardAdminEdit($user);

        $user->load('providerProfile');

        $bookingsCount = Booking::where('customer_id', $user->id)->count();
        $servicesCount = 0;
        $providerSetup = null;

        if ($user->isProvider() && $user->providerProfile) {
            $servicesCount = Service::where('provider_id', $user->providerProfile->id)->count();
            $providerSetup = [
                'profile_complete' => !empty($user->providerProfile->bio) && !empty($user->providerProfile->specialization),
                'services_added'   => $servicesCount > 0,
                'availability_set' => $user->providerProfile->availabilities()->exists(),
            ];
        }

        return view('admin.users.show', compact('user', 'bookingsCount', 'servicesCount', 'providerSetup'));
    }

    /**
     * Show the edit form for a specific user.
     */
    public function edit(User $user): View
    {
        $this->guardAdminEdit($user);
        $user->load('providerProfile');
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user information.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $this->guardAdminEdit($user);

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'unique:users,email,' . $user->id],
            'role'         => ['required', 'in:customer,provider'],
            'phone_number' => ['nullable', 'string', 'max:20'],
        ]);

        $oldRole = $user->role;
        $oldEmail = $user->email;

        $user->update($validated);

        // If role changed TO provider, create/update provider record
        if ($validated['role'] === 'provider' && $oldRole !== 'provider') {
            Provider::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'owner_name'        => $user->name,
                    'email'             => $user->email,
                    'phone'             => $user->phone_number ?? '',
                    'business_name'     => $user->name . "'s Business",
                    'business_category' => 'Consultant',
                    'specialization'    => 'General Professional',
                    'bio'               => 'New provider profile.',
                    'status'            => 'pending', // Reverted to pending for manual approval
                ]
            );
        }

        // If email changed, sync provider record
        if ($oldEmail !== $validated['email'] && $user->isProvider()) {
            Provider::where('email', $oldEmail)->update(['email' => $validated['email']]);
        }

        // Sync provider name/phone
        if ($user->isProvider() && $user->providerProfile) {
            $user->providerProfile->update([
                'name'  => $user->name,
                'phone' => $user->phone_number ?? $user->providerProfile->phone,
            ]);
        }

        Log::info("Admin [{$this->adminName()}] updated user #{$user->id} ({$user->email})");

        // Notify user of profile update
        $user->notify(new \App\Notifications\BookingNotification(
            'Account Updated',
            "Your account details have been updated by an administrator.",
            route('settings')
        ));

        return redirect()->route('admin.users.index')
            ->with('success', "User \"{$user->name}\" updated successfully.");
    }

    /**
     * Delete a user and clean up related records.
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->guardAdminEdit($user);

        $userName = $user->name;
        $userEmail = $user->email;

        // Cancel active bookings
        Booking::where('customer_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->update(['status' => 'cancelled']);

        // If provider, soft-delete their services and cancel their appointments
        if ($user->isProvider() && $user->providerProfile) {
            Service::where('provider_id', $user->providerProfile->id)->delete();
            Booking::where('provider_id', $user->providerProfile->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->update(['status' => 'cancelled']);
            $user->providerProfile->delete();
        }

        // Delete notifications
        $user->notifications()->delete();

        // Delete user
        $user->delete();

        Log::warning("Admin [{$this->adminName()}] DELETED user #{$user->id} ({$userEmail})");

        return redirect()->route('admin.users.index')
            ->with('success', "User \"{$userName}\" has been permanently deleted.");
    }

    /**
     * Reset a user's password to a temporary one.
     */
    public function resetPassword(User $user): RedirectResponse
    {
        $this->guardAdminEdit($user);

        $tempPassword = 'BookEase@' . rand(1000, 9999);
        $user->update(['password' => Hash::make($tempPassword)]);

        Log::info("Admin [{$this->adminName()}] reset password for user #{$user->id} ({$user->email})");

        // Use the professional OtpVerificationMail view for the temp password delivery
        Mail::to($user->email)->send(new \App\Mail\OtpVerificationMail($tempPassword));

        return back()->with('success', "Password reset for \"{$user->name}\". Temporary password sent to their email.");
    }

    /**
     * Display a listing of providers with status filtering.
     */
    public function providers(Request $request): View
    {
        $status = $request->input('status', 'pending');
        
        $query = Provider::query();
        $query->where('status', $status);
        
        if ($search = $request->input('search')) {
            $query->whereNested(function ($q) use ($search) {
                $q->where('owner_name', 'like', "%{$search}%")
                  ->orWhere('business_name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $providers = $query->with('user')->latest()->paginate(15)->withQueryString();

        return view('admin.providers.index', compact('providers'));
    }

    /**
     * Approve a pending provider.
     */
    public function approveProvider(User $user): RedirectResponse
    {
        $this->guardAdminEdit($user);

        if (!$user->isProvider() || !$user->providerProfile) {
            return back()->with('error', 'This action is only available for providers (No profile found).');
        }

        try {
            DB::transaction(function () use ($user) {
                $user->update(['status' => 'active']);
                $user->providerProfile->update(['status' => 'approved']);
            });

            Log::info("Admin [{$this->adminName()}] APPROVED provider #{$user->providerProfile->id}");

            // Send dedicated status notification
            $user->notify(new ProviderStatusNotification('approved'));

            return back()->with('success', "Provider \"{$user->name}\" application approved.");
        } catch (\Exception $e) {
            Log::error("Failed to approve provider #{$user->id}: " . $e->getMessage());
            return back()->with('error', 'Failed to approve provider: ' . $e->getMessage());
        }
    }

    /**
     * Reject a pending provider.
     */
    public function rejectProvider(User $user, Request $request): RedirectResponse
    {
        $this->guardAdminEdit($user);

        if (!$user->isProvider() || !$user->providerProfile) {
            return back()->with('error', 'This action is only available for providers (No profile found).');
        }

        try {
            DB::transaction(function () use ($user) {
                $user->update(['status' => 'rejected']);
                $user->providerProfile->update(['status' => 'rejected']);
            });

            Log::warning("Admin [{$this->adminName()}] REJECTED provider #{$user->providerProfile->id}");

            // Send dedicated status notification
            $user->notify(new ProviderStatusNotification('rejected'));

            return back()->with('success', "Provider \"{$user->name}\" application rejected.");
        } catch (\Exception $e) {
            Log::error("Failed to reject provider #{$user->id}: " . $e->getMessage());
            return back()->with('error', 'Failed to reject provider: ' . $e->getMessage());
        }
    }

    /**
     * Toggle a provider's approval status (Suspend/Approve).
     */
    public function toggleProviderStatus(User $user): RedirectResponse
    {
        $this->guardAdminEdit($user);

        if (!$user->isProvider() || !$user->providerProfile) {
            return back()->with('error', 'This action is only available for providers (No profile found).');
        }

        $isApproved = $user->providerProfile->status === 'approved';
        $newProviderStatus = $isApproved ? 'suspended' : 'approved';
        $newUserStatus = $isApproved ? 'suspended' : 'active';

        DB::transaction(function () use ($user, $newProviderStatus, $newUserStatus) {
            $user->update(['status' => $newUserStatus]);
            $user->providerProfile->update(['status' => $newProviderStatus]);
        });

        Log::info("Admin [{$this->adminName()}] set provider #{$user->providerProfile->id} status to {$newProviderStatus}");

        $user->notify(new ProviderStatusNotification($newProviderStatus));

        return back()->with('success', "Provider status set to \"{$newProviderStatus}\".");
    }

    /**
     * Manually verify a user's email.
     */
    public function verifyEmail(User $user): RedirectResponse
    {
        $this->guardAdminEdit($user);

        $user->update(['email_verified_at' => now()]);

        Log::info("Admin [{$this->adminName()}] manually verified email for user #{$user->id}");

        return back()->with('success', "Email verified for \"{$user->name}\".");
    }

    /**
     * Security Guard: Prevent admin from editing another admin.
     */
    private function guardAdminEdit(User $user): void
    {
        if ($user->isAdmin()) {
            abort(403, 'Admin accounts cannot be managed through this interface.');
        }
    }

    /**
     * Helper: Get current admin name for audit logs.
     */
    private function adminName(): string
    {
        return Auth::user()->name ?? 'Unknown Admin';
    }
    /**
     * Display a listing of customers.
     */
    public function customers(Request $request): View
    {
        $query = User::where('role', 'customer');

        if ($search = $request->input('search')) {
            /** @var \Illuminate\Database\Query\Builder $query */
            $query->whereNested(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users.customers', compact('users'));
    }
}
