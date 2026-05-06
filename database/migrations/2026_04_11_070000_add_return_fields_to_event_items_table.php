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
    Schema::table('event_items', function (Blueprint $table) {
        if (!Schema::hasColumn('event_items', 'return_destination')) {
            $table->enum('return_destination', ['warehouse','cleaning','repair'])->nullable()->after('returned_by');
        }
        if (!Schema::hasColumn('event_items', 'condition_on_return')) {
            $table->tinyInteger('condition_on_return')->nullable()->after('return_destination');
        }
        if (!Schema::hasColumn('event_items', 'return_notes')) {
            $table->text('return_notes')->nullable()->after('condition_on_return');
        }
        if (!Schema::hasColumn('event_items', 'return_processed')) {
            $table->boolean('return_processed')->default(false)->after('return_notes');
        }
    });
}
    /**
     * Reverse the migrations.
     */
   public function down(): void
{
    Schema::table('event_items', function (Blueprint $table) {
        $columns = ['return_destination','condition_on_return','return_notes','return_processed'];
        foreach ($columns as $col) {
            if (Schema::hasColumn('event_items', $col)) {
                $table->dropColumn($col);
            }
        }
    });
}
};