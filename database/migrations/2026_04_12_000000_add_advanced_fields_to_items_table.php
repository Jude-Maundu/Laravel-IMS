<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('brand')->nullable()->after('category');
            $table->string('model_number')->nullable()->after('brand');
            $table->string('serial_number')->nullable()->after('model_number');
            $table->date('purchase_date')->nullable()->after('serial_number');
            $table->decimal('purchase_cost', 12, 2)->nullable()->after('purchase_date');
            $table->text('specifications')->nullable()->after('purchase_cost');
            $table->string('dimensions')->nullable()->after('specifications');
            $table->string('weight')->nullable()->after('dimensions');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn([
                'brand', 'model_number', 'serial_number', 
                'purchase_date', 'purchase_cost', 'specifications',
                'dimensions', 'weight'
            ]);
        });
    }
};
