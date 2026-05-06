<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemPiecesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = Item::all();

        // Use a seeded random for consistency
        mt_srand(42);

        // Track last number for each shortcode to avoid DB lookups
        $shortCodeCounters = [];

        foreach ($items as $item) {
            // Generate random total_pieces between 6 and 45
            $totalPieces = mt_rand(6, 45);

            // Update the item's total_pieces field
            $item->update(['total_pieces' => $totalPieces]);

            // Generate shortcode
            $shortCode = Item::generateShortCode($item->name);

            // Initialize counter for this shortcode if not exists
            if (!isset($shortCodeCounters[$shortCode])) {
                $shortCodeCounters[$shortCode] = 0;
            }

            // Generate pieces for this item
            $piecesData = [];
            for ($i = 1; $i <= $totalPieces; $i++) {
                $shortCodeCounters[$shortCode]++;
                $uniqueCode = sprintf('GA-%s-%03d', $shortCode, $shortCodeCounters[$shortCode]);

                $piecesData[] = [
                    'item_id' => $item->id,
                    'unique_code' => $uniqueCode,
                    'status' => 'Available',
                    'condition_score' => null,
                    'current_event_id' => null,
                    'notes' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Batch insert
            \App\Models\ItemPiece::insert($piecesData);

            $this->command->info("Generated {$totalPieces} pieces for: {$item->name}");
        }

        $this->command->info('Item pieces generated successfully for ' . $items->count() . ' items.');
    }
}
