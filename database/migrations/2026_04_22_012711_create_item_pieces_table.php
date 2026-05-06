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
        Schema::create('item_pieces', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->string('unique_code')->unique();
            $table->enum('status', ['Available', 'Assigned', 'Cleaning', 'Under Repair', 'Damaged', 'Written Off'])->default('Available');
            $table->tinyInteger('condition_score')->nullable();
            $table->unsignedBigInteger('current_event_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('current_event_id')->references('id')->on('events')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_pieces');
    }
};
