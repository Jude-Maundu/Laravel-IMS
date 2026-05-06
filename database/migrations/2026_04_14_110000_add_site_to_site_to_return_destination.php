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
            // For PostgreSQL: Add new value to existing enum type
            DB::statement("ALTER TYPE return_destination ADD VALUE 'site-to-site'");
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
            // For PostgreSQL: Recreate enum without the added value
            DB::statement("CREATE TYPE return_destination_old AS ENUM('warehouse', 'cleaning', 'repair')");
            DB::statement("ALTER TABLE event_items ALTER COLUMN return_destination TYPE return_destination_old USING return_destination::text::return_destination_old");
            DB::statement("DROP TYPE return_destination");
            DB::statement("ALTER TYPE return_destination_old RENAME TO return_destination");
        } elseif ($driver === 'mysql') {
            // For MySQL: Revert enum
            DB::statement("ALTER TABLE event_items MODIFY COLUMN return_destination ENUM('warehouse', 'cleaning', 'repair') NULL");
        }
    }
};
