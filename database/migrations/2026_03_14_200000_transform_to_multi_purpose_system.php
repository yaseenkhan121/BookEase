<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Transform Users Table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('active')->after('role');
            }
        });

        // 2. Transform Providers Table
        Schema::table('providers', function (Blueprint $table) {
            // Add identifying and industry-specific fields
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
            $table->string('business_name')->nullable()->after('user_id');
            $table->string('business_category')->nullable()->after('business_name');
            $table->string('address')->nullable()->after('phone');
            $table->string('city')->nullable()->after('address');
            $table->string('country')->nullable()->after('city');
            $table->string('logo')->nullable()->after('profile_image');
            
            // Rename for clarity
            if (Schema::hasColumn('providers', 'name')) {
                $table->renameColumn('name', 'owner_name');
            }
        });

        // 3. Transform Services Table
        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'service_name')) {
                $table->renameColumn('service_name', 'name');
            }
            if (Schema::hasColumn('services', 'duration')) {
                $table->renameColumn('duration', 'duration_minutes');
            }
        });

        // 4. Transform Appointments to Bookings
        if (Schema::hasTable('appointments')) {
            Schema::rename('appointments', 'bookings');
        }

        // 5. Create Custom Notifications Table
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('title');
                $table->text('message');
                $table->string('type'); // booking, system, service
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->boolean('is_read')->default(false);
                $table->timestamps();
            });
        }

        // Data Migration: Link existing providers to users via email
        $providers = DB::table('providers')->get();
        foreach ($providers as $provider) {
            $user = DB::table('users')->where('email', $provider->email)->first();
            if ($user) {
                DB::table('providers')->where('id', $provider->id)->update([
                    'user_id' => optional($user)->id,
                    'business_name' => $provider->owner_name // Use owner name as default business name
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
        
        if (Schema::hasTable('bookings')) {
            Schema::rename('bookings', 'appointments');
        }

        Schema::table('services', function (Blueprint $table) {
            $table->renameColumn('name', 'service_name');
            $table->renameColumn('duration_minutes', 'duration');
        });

        Schema::table('providers', function (Blueprint $table) {
            $table->renameColumn('owner_name', 'name');
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'business_name', 'business_category', 'address', 'city', 'country', 'logo']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
