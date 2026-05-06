<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("repairs", function (Blueprint $table) {
            $table->id();
            $table->foreignId("item_id")->constrained()->onDelete("cascade");
            $table->string("repair_type")->nullable(); // Scheduled, Emergency
            $table->string("description");
            $table->decimal("estimated_cost", 10, 2)->nullable();
            $table->decimal("actual_cost", 10, 2)->nullable();
            $table->string("status")->default("Pending"); // Pending, In Progress, Completed, Cancelled
            $table->date("started_at")->nullable();
            $table->date("completed_at")->nullable();
            $table->string("technician_name")->nullable();
            $table->text("notes")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("repairs");
    }
};
