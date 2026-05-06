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
        Schema::create('event_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('condition_on_dispatch')->nullable()->comment('1=Poor 2=Average 3=Fair 4=Good 5=Excellent');
            $table->tinyInteger('condition_on_return')->nullable();
            $table->text('dispatch_notes')->nullable();
            $table->text('return_notes')->nullable();
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->unsignedBigInteger('dispatched_by')->nullable();
            $table->unsignedBigInteger('returned_by')->nullable();
            $table->foreign('dispatched_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('returned_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['event_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_items');
    }
};
