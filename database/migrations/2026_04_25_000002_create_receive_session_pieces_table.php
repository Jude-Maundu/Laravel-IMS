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
        Schema::create('receive_session_pieces', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('receive_session_id');
            $table->unsignedBigInteger('item_piece_id');
            $table->string('unique_code');
            $table->unsignedBigInteger('item_id');
            $table->tinyInteger('condition_score')->nullable();
            $table->enum('destination', ['warehouse', 'cleaning', 'repair'])->default('warehouse');
            $table->text('damage_note')->nullable();
            $table->unsignedBigInteger('received_by');
            $table->timestamp('received_at');
            $table->timestamps();

            $table->foreign('receive_session_id')->references('id')->on('receive_sessions')->onDelete('cascade');
            $table->foreign('item_piece_id')->references('id')->on('item_pieces')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('items');
            $table->foreign('received_by')->references('id')->on('users');

            // Prevent same piece being received twice in same session
            $table->unique(['receive_session_id', 'item_piece_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receive_session_pieces');
    }
};
