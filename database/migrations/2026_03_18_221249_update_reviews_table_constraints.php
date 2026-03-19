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
        Schema::table('reviews', function (Blueprint $table) {
            // Rename comment to review_text if it exists
            if (Schema::hasColumn('reviews', 'comment')) {
                $table->renameColumn('comment', 'review_text');
            }
            
            // Make booking_id unique
            // First drop existing non-unique index if needed, but in standard Laravel it might not have one or just the foreign key.
            // We'll just add the unique constraint.
            $table->unique('booking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropUnique(['booking_id']);
            $table->renameColumn('review_text', 'comment');
        });
    }
};
