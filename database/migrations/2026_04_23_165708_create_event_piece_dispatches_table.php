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
        Schema::create('event_piece_dispatches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('item_piece_id');
            $table->tinyInteger('condition_on_dispatch')->nullable();
            $table->timestamp('dispatched_at');
            $table->unsignedBigInteger('dispatched_by')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('item_piece_id')->references('id')->on('item_pieces')->onDelete('cascade');
            $table->foreign('dispatched_by')->references('id')->on('users')->onDelete('set null');

            // Index for performance
            $table->index(['event_id', 'item_piece_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_piece_dispatches');
    }
};
