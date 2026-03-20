<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('provider_id')->constrained('providers')->onDelete('cascade');
            $table->string('payment_method'); // easypaisa, jazzcash, bank_transfer
            $table->string('transaction_reference')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('PKR');
            $table->string('payment_status')->default('pending');
            // pending, verification_pending, paid, failed, refunded
            $table->string('payment_proof')->nullable(); // file path for bank transfer receipts
            $table->foreignId('verified_by_admin')->nullable()->constrained('users')->onDelete('set null');
            $table->text('admin_notes')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'payment_status']);
            $table->index('payment_method');
        });

        // Add pending_payment status to bookings
        // The column is already enum, we need to update it
        if (Schema::hasTable('bookings')) {
            if (\Illuminate\Support\Facades\DB::connection()->getDriverName() === 'mysql') {
                \Illuminate\Support\Facades\DB::statement(
                    "ALTER TABLE bookings MODIFY COLUMN status ENUM('pending','pending_payment','confirmed','in_progress','completed','cancelled','rejected','approved') DEFAULT 'pending'"
                );
            } elseif (\Illuminate\Support\Facades\DB::connection()->getDriverName() === 'pgsql') {
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE bookings ALTER COLUMN status TYPE VARCHAR(255)");
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE bookings ALTER COLUMN status SET DEFAULT 'pending'");
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
