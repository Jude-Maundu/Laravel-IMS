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
        // Modify the enum to include 'site-to-site'
        DB::statement("ALTER TABLE event_items MODIFY COLUMN return_destination ENUM('warehouse', 'cleaning', 'repair', 'site-to-site') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE event_items MODIFY COLUMN return_destination ENUM('warehouse', 'cleaning', 'repair') NULL");
    }
};
