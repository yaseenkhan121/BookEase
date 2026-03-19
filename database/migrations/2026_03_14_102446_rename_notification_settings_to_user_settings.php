<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('notification_settings', 'user_settings');

        Schema::table('user_settings', function (Blueprint $table) {
            // Mapping existing columns to requested names or adding new ones
            if (Schema::hasColumn('user_settings', 'booking_updates')) {
                $table->renameColumn('booking_updates', 'booking_notifications');
            } else {
                $table->boolean('booking_notifications')->default(true);
            }

            if (Schema::hasColumn('user_settings', 'system_alerts')) {
                $table->renameColumn('system_alerts', 'reminder_notifications');
            } else {
                $table->boolean('reminder_notifications')->default(true);
            }

            if (!Schema::hasColumn('user_settings', 'email_notifications')) {
                $table->boolean('email_notifications')->default(true);
            }

            // Remove unneeded columns
            $table->dropColumn(['marketing_emails']);
        });
    }

    public function down(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->renameColumn('booking_notifications', 'booking_updates');
            $table->renameColumn('reminder_notifications', 'system_alerts');
            $table->boolean('marketing_emails')->default(false);
        });

        Schema::rename('user_settings', 'notification_settings');
    }
};
