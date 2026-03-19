<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Performance Indexes: Add indexes to frequently queried columns
     * that are not already covered by foreign key constraints.
     */
    public function up(): void
    {
        // Bookings: status is filtered in almost every query
        Schema::table('bookings', function (Blueprint $table) {
            $table->index('status');
            $table->index('start_time');
            $table->index('created_at');
        });

        // Services: status filtered in marketplace views
        Schema::table('services', function (Blueprint $table) {
            $table->index('status');
        });

        // Providers: status + setup_completed + business_category filtered for browsing
        Schema::table('providers', function (Blueprint $table) {
            $table->index('status');
            $table->index('setup_completed');
            $table->index('business_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['start_time']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('providers', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['setup_completed']);
            $table->dropIndex(['business_category']);
        });
    }
};
