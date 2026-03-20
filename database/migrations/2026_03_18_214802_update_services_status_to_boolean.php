<?php
// NO-OP: services status is already boolean in base create_services_table migration.
use Illuminate\Database\Migrations\Migration;
return new class extends Migration {
    public function up(): void { }
    public function down(): void { }
};
