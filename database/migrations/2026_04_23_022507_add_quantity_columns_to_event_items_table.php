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
        Schema::table('event_items', function (Blueprint $table) {
            $table->unsignedInteger('quantity_requested')->default(1)->after('item_id');
            $table->unsignedInteger('quantity_dispatched')->default(0)->after('quantity_requested');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_items', function (Blueprint $table) {
            $table->dropColumn(['quantity_requested', 'quantity_dispatched']);
        });
    }
};
