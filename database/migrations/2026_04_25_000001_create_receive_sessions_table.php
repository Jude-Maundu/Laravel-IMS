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
        Schema::create('receive_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->string('session_token', 64)->unique();
            $table->unsignedBigInteger('created_by');
            $table->timestamp('expires_at');
            $table->enum('status', ['active', 'completed', 'expired', 'cancelled'])->default('active');
            $table->unsignedInteger('received_count')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->string('receive_ref')->nullable();
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receive_sessions');
    }
};
