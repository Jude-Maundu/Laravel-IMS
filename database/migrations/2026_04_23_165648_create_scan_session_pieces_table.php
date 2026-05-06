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
        Schema::create('scan_session_pieces', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('scan_session_id');
            $table->unsignedBigInteger('item_piece_id');
            $table->string('unique_code')->index();
            $table->unsignedBigInteger('item_id');
            $table->tinyInteger('condition_score')->nullable();
            $table->unsignedBigInteger('scanned_by')->nullable();
            $table->timestamp('scanned_at');
            $table->timestamps();

            // Foreign keys
            $table->foreign('scan_session_id')->references('id')->on('scan_sessions')->onDelete('cascade');
            $table->foreign('item_piece_id')->references('id')->on('item_pieces')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('scanned_by')->references('id')->on('users')->onDelete('set null');

            // Unique constraint — prevent same piece being scanned twice in same session
            $table->unique(['scan_session_id', 'item_piece_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scan_session_pieces');
    }
};
