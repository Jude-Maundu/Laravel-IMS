<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OperationalItem;

class OperationalItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['name' => 'Helmets', 'category' => 'Safety', 'sort_order' => 1],
            ['name' => 'Reflectors', 'category' => 'Safety', 'sort_order' => 2],
            ['name' => 'Harness', 'category' => 'Safety', 'sort_order' => 3],
            ['name' => 'Binding Wire', 'category' => 'Tools', 'sort_order' => 4],
            ['name' => 'Insulating Tape', 'category' => 'Tools', 'sort_order' => 5],
            ['name' => 'Knockout Tape', 'category' => 'Tools', 'sort_order' => 6],
            ['name' => 'Lighting Cables', 'category' => 'Electrical', 'sort_order' => 7],
            ['name' => 'Skip Tailing', 'category' => 'Electrical', 'sort_order' => 8],
            ['name' => 'Gaffer Tape', 'category' => 'Consumables', 'sort_order' => 9],
            ['name' => 'Cable Ties', 'category' => 'Consumables', 'sort_order' => 10],
            ['name' => 'Walkie Talkie', 'category' => 'Communication', 'sort_order' => 11],
        ];

        foreach ($items as $item) {
            OperationalItem::firstOrCreate(
                ['name' => $item['name']],
                [
                    'category' => $item['category'],
                    'sort_order' => $item['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
