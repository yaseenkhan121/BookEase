<?php

namespace App\Http\Controllers; // Ensure this matches your file location

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class NotificationController extends Controller
{
    /**
     * Display the notifications center.
     */
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Paginate for performance (Senior Logic: latest() is a shorthand for orderBy created_at DESC)
        $notifications = $user->notifications()
            ->latest()
            ->paginate(10);
        
        // This looks for: resources/views/notifications/index.blade.php
        return view('notifications.index', [
            'notifications' => $notifications,
            'unreadCount'   => $user->unreadNotifications->count()
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Notification marked as read.']);
        }

        // If the notification data contains a specific URL (like an appointment link), go there
        if (isset($notification->data['action_url']) && $notification->data['action_url'] !== '#') {
            return redirect($notification->data['action_url']);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read at once.
     */
    public function markAllRead(Request $request)
    {
        Auth::user()->unreadNotifications->markAsRead();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'All notifications marked as read.']);
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Delete a single notification.
     */
    public function destroy(Request $request, $id)
    {
        // Senior Logic: Only allow deletion of notifications belonging to the logged-in user
        $notification = Auth::user()->notifications()->find($id);

        if ($notification) {
            $notification->delete();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Notification removed.']);
            }

            return back()->with('success', 'Notification removed.');
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Unable to find that notification.'], 404);
        }

        return back()->with('error', 'Unable to find that notification.');
    }
}