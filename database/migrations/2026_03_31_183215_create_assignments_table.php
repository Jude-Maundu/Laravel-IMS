<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("assignments", function (Blueprint $table) {
            $table->id();
            $table->foreignId("item_id")->constrained()->onDelete("cascade");
            $table->string("assigned_to");
            $table->string("assigned_by");
            $table->date("due_date")->nullable();
            $table->date("returned_at")->nullable();
            $table->string("status")->default("Active"); // Active, Returned, Overdue
            $table->text("notes")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("assignments");
    }
};
