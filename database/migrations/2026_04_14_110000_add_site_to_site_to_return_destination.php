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
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            // For PostgreSQL: Use a new enum type name instead of assuming the existing type name.
            DB::statement("CREATE TYPE return_destination_new_20260414 AS ENUM('warehouse', 'cleaning', 'repair', 'site-to-site')");
            DB::statement("ALTER TABLE event_items ALTER COLUMN return_destination TYPE return_destination_new_20260414 USING return_destination::text::return_destination_new_20260414");
            DB::statement("DROP TYPE IF EXISTS event_items_return_destination_enum");
            DB::statement("ALTER TYPE return_destination_new_20260414 RENAME TO event_items_return_destination_enum");
        } elseif ($driver === 'mysql') {
            // For MySQL: Modify column enum
            DB::statement("ALTER TABLE event_items MODIFY COLUMN return_destination ENUM('warehouse', 'cleaning', 'repair', 'site-to-site') NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("CREATE TYPE return_destination_old_20260414 AS ENUM('warehouse', 'cleaning', 'repair')");
            DB::statement("ALTER TABLE event_items ALTER COLUMN return_destination TYPE return_destination_old_20260414 USING return_destination::text::return_destination_old_20260414");
            DB::statement("DROP TYPE IF EXISTS event_items_return_destination_enum");
            DB::statement("ALTER TYPE return_destination_old_20260414 RENAME TO event_items_return_destination_enum");
        } elseif ($driver === 'mysql') {
            // For MySQL: Revert enum
            DB::statement("ALTER TABLE event_items MODIFY COLUMN return_destination ENUM('warehouse', 'cleaning', 'repair') NULL");
        }
    }
};
