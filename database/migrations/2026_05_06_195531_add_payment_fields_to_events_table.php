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
        Schema::table('events', function (Blueprint $table) {
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending')->after('status');
            $table->decimal('amount_due', 12, 2)->nullable()->after('payment_status');
            $table->string('customer_phone')->nullable()->after('amount_due');
            $table->string('transaction_id')->nullable()->after('customer_phone');
            $table->foreignId('customer_id')->nullable()->after('transaction_id')->constrained('users')->nullOnDelete();
            
            // Modify status enum to include 'Awaiting Payment'
            // In MySQL we can't easily modify ENUM via Schema::table without raw SQL or recreating.
            // But since we are in development, maybe I can just add it.
        });
        
        // Use raw SQL to update the enum
        DB::statement("ALTER TABLE events MODIFY COLUMN status ENUM('Draft', 'Awaiting Payment', 'Scheduled', 'Active', 'Set Down', 'Completed', 'Cancelled') DEFAULT 'Draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn(['payment_status', 'amount_due', 'customer_phone', 'transaction_id', 'customer_id']);
        });
        
        DB::statement("ALTER TABLE events MODIFY COLUMN status ENUM('Draft', 'Scheduled', 'Active', 'Set Down', 'Completed', 'Cancelled') DEFAULT 'Draft'");
    }
};
