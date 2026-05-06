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
        Schema::table('event_piece_dispatches', function (Blueprint $table) {
            $table->tinyInteger('condition_on_return')->nullable()->after('condition_on_dispatch');
            $table->enum('return_destination', ['warehouse', 'cleaning', 'repair'])->nullable()->after('condition_on_return');
            $table->timestamp('returned_at')->nullable()->after('dispatched_at');
            $table->unsignedBigInteger('returned_by')->nullable()->after('returned_at');

            $table->foreign('returned_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_piece_dispatches', function (Blueprint $table) {
            $table->dropForeign(['returned_by']);
            $table->dropColumn(['condition_on_return', 'return_destination', 'returned_at', 'returned_by']);
        });
    }
};
