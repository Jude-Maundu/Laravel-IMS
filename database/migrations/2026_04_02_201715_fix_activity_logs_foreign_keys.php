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
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn(['item_id', 'user_id']);
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->foreignId('item_id')->nullable()->constrained('items')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['item_id', 'user_id']);
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->string('item_id');
            $table->string('user_id');
        });
    }
};
