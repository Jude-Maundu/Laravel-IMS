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
        Schema::create('missing_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('item_piece_id')->nullable();
            $table->string('unique_code');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('marked_by');
            $table->timestamp('marked_at');
            $table->text('notes')->nullable();
            $table->enum('status', ['missing', 'found', 'written_off'])->default('missing');
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('item_piece_id')->references('id')->on('item_pieces')->onDelete('set null');
            $table->foreign('item_id')->references('id')->on('items');
            $table->foreign('marked_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('missing_items');
    }
};
