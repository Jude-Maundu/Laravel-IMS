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
        Schema::create('event_item_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_item_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->enum('type', ['dispatch', 'return'])->default('dispatch');
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->foreign('uploaded_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_item_images');
    }
};
