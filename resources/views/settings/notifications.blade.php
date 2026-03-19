@extends('settings.layout')

@section('settings_content')
<div class="mb-4">
    <h4 class="section-title mb-1">Notification Preferences</h4>
    <p class="section-subtitle">Choose what we get to notify you about.</p>

    <form action="{{ route('settings.notifications.update') }}" method="POST" data-ajax="true">
        @csrf
        <div class="setting-list">
            {{-- Email Notifications --}}
            <div class="setting-row">
                <div class="setting-info">
                    <span class="setting-title font-weight-bold">Email Notifications</span>
                    <span class="setting-desc">Receive general email notifications about your account, security, and updates.</span>
                </div>
                <label class="premium-switch">
                    <input type="checkbox" name="email_notifications" {{ $settings->email_notifications ? 'checked' : '' }}>
                    <span class="premium-slider"></span>
                </label>
            </div>

            {{-- Booking Notifications --}}
            <div class="setting-row">
                <div class="setting-info">
                    <span class="setting-title font-weight-bold">Booking Notifications</span>
                    <span class="setting-desc">Get notified when a booking is created, updated, or when a reminder is sent.</span>
                </div>
                <label class="premium-switch">
                    <input type="checkbox" name="booking_notifications" {{ $settings->booking_notifications ? 'checked' : '' }}>
                    <span class="premium-slider"></span>
                </label>
            </div>

            {{-- Reminder Notifications --}}
            <div class="setting-row">
                <div class="setting-info">
                    <span class="setting-title font-weight-bold">Reminder Notifications</span>
                    <span class="setting-desc">Receive alerts for upcoming appointments and important system announcements.</span>
                </div>
                <label class="premium-switch">
                    <input type="checkbox" name="reminder_notifications" {{ $settings->reminder_notifications ? 'checked' : '' }}>
                    <span class="premium-slider"></span>
                </label>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-3 mt-5 pb-3">
            <button type="button" class="btn btn-secondary-modern">Cancel</button>
            <button type="submit" class="btn btn-save-modern" data-loading-text="Saving...">Save Preferences</button>
        </div>
    </form>
</div>

@endsection
