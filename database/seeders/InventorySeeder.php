<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Item::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $defaults = [
            'status'          => 'Available',
            'location'        => 'Warehouse',
            'assigned_to'     => null,
            'assigned_by'     => null,
            'last_updated_by' => 'System',
            'last_updated_at' => now(),
            'notes'           => null,
        ];

        $inventory = [

            // ── Tents - 30 Span ────────────────────────────────────────
            ['name' => 'Main Beam / Chase',   'category' => 'Tents - 30 Span'],
            ['name' => 'Rafters',             'category' => 'Tents - 30 Span'],
            ['name' => 'Gable Ceiling',       'category' => 'Tents - 30 Span'],
            ['name' => 'Stands',              'category' => 'Tents - 30 Span'],
            ['name' => 'Zips',                'category' => 'Tents - 30 Span'],
            ['name' => 'Ridge Beam',          'category' => 'Tents - 30 Span'],
            ['name' => 'Roof Purlins',        'category' => 'Tents - 30 Span'],
            ['name' => 'Side Purlins',        'category' => 'Tents - 30 Span'],
            ['name' => 'Ceiling Rope',        'category' => 'Tents - 30 Span'],
            ['name' => 'Connectors',          'category' => 'Tents - 30 Span'],
            ['name' => 'Roof Cover (Top)',    'category' => 'Tents - 30 Span'],
            ['name' => 'Gable Stand A',       'category' => 'Tents - 30 Span'],
            ['name' => 'Gable Stand B',       'category' => 'Tents - 30 Span'],
            ['name' => 'Gable Stand C',       'category' => 'Tents - 30 Span'],
            ['name' => 'Gable Stand AA',      'category' => 'Tents - 30 Span'],
            ['name' => 'Gable Stand BB',      'category' => 'Tents - 30 Span'],
            ['name' => 'Side Walls (Flaps)',  'category' => 'Tents - 30 Span'],
            ['name' => 'Base Plates',         'category' => 'Tents - 30 Span'],
            ['name' => 'Bags',                'category' => 'Tents - 30 Span'],
            ['name' => 'Pocketers',           'category' => 'Tents - 30 Span'],
            ['name' => 'Angles',              'category' => 'Tents - 30 Span'],
            ['name' => 'Gable Bars',          'category' => 'Tents - 30 Span'],
            ['name' => 'Ratchet Straps',      'category' => 'Tents - 30 Span'],
            ['name' => 'Purlin Connectors',   'category' => 'Tents - 30 Span'],
            ['name' => 'Clamps',              'category' => 'Tents - 30 Span'],
            ['name' => 'Bolts and Nuts',      'category' => 'Tents - 30 Span'],
            ['name' => 'Ceiling Rods',        'category' => 'Tents - 30 Span'],
            ['name' => 'X-Bar (Makasi)',      'category' => 'Tents - 30 Span'],
            ['name' => 'Screws',              'category' => 'Tents - 30 Span'],
            ['name' => 'Gable Canvas',        'category' => 'Tents - 30 Span'],
            ['name' => 'Curtains',            'category' => 'Tents - 30 Span'],
            ['name' => 'Ceilings',            'category' => 'Tents - 30 Span'],
            ['name' => 'USB 30 Span',         'category' => 'Tents - 30 Span'],
            ['name' => 'USB 20 Span',         'category' => 'Tents - 30 Span'],

            // ── Tents - 20 Span ────────────────────────────────────────
            ['name' => 'Main Beam / Chase',   'category' => 'Tents - 20 Span'],
            ['name' => 'Rafters',             'category' => 'Tents - 20 Span'],
            ['name' => 'Gable Ceiling',       'category' => 'Tents - 20 Span'],
            ['name' => 'Stands',              'category' => 'Tents - 20 Span'],
            ['name' => 'Ridge Beam',          'category' => 'Tents - 20 Span'],
            ['name' => 'Roof Purlins',        'category' => 'Tents - 20 Span'],
            ['name' => 'Side Purlins',        'category' => 'Tents - 20 Span'],
            ['name' => 'Roof Cover (Top)',    'category' => 'Tents - 20 Span'],
            ['name' => 'Side Walls (Flaps)',  'category' => 'Tents - 20 Span'],
            ['name' => 'Base Plates',         'category' => 'Tents - 20 Span'],
            ['name' => 'Angles',              'category' => 'Tents - 20 Span'],
            ['name' => 'Gable Canvas',        'category' => 'Tents - 20 Span'],
            ['name' => 'Curtains',            'category' => 'Tents - 20 Span'],
            ['name' => 'Ceilings',            'category' => 'Tents - 20 Span'],

            // ── Tents - 15 Span ────────────────────────────────────────
            ['name' => 'Main Beam / Chase',   'category' => 'Tents - 15 Span'],
            ['name' => 'Rafters',             'category' => 'Tents - 15 Span'],
            ['name' => 'Gable Ceiling',       'category' => 'Tents - 15 Span'],
            ['name' => 'Stands',              'category' => 'Tents - 15 Span'],
            ['name' => 'Ridge Beam',          'category' => 'Tents - 15 Span'],
            ['name' => 'Roof Purlins',        'category' => 'Tents - 15 Span'],
            ['name' => 'Side Purlins',        'category' => 'Tents - 15 Span'],
            ['name' => 'Roof Cover (Top)',    'category' => 'Tents - 15 Span'],
            ['name' => 'Side Walls (Flaps)',  'category' => 'Tents - 15 Span'],
            ['name' => 'Base Plates',         'category' => 'Tents - 15 Span'],
            ['name' => 'Angles',              'category' => 'Tents - 15 Span'],
            ['name' => 'Gable Canvas',        'category' => 'Tents - 15 Span'],
            ['name' => 'Curtains',            'category' => 'Tents - 15 Span'],
            ['name' => 'Ceilings',            'category' => 'Tents - 15 Span'],

            // ── Tents - 10 Span ────────────────────────────────────────
            ['name' => 'Main Beam / Chase',   'category' => 'Tents - 10 Span'],
            ['name' => 'Rafters',             'category' => 'Tents - 10 Span'],
            ['name' => 'Gable Ceiling',       'category' => 'Tents - 10 Span'],
            ['name' => 'Stands',              'category' => 'Tents - 10 Span'],
            ['name' => 'Ridge Beam',          'category' => 'Tents - 10 Span'],
            ['name' => 'Roof Purlins',        'category' => 'Tents - 10 Span'],
            ['name' => 'Side Purlins',        'category' => 'Tents - 10 Span'],
            ['name' => 'Roof Cover (Top)',    'category' => 'Tents - 10 Span'],
            ['name' => 'Side Walls (Flaps)',  'category' => 'Tents - 10 Span'],
            ['name' => 'Base Plates',         'category' => 'Tents - 10 Span'],
            ['name' => 'Angles',              'category' => 'Tents - 10 Span'],
            ['name' => 'Gable Canvas',        'category' => 'Tents - 10 Span'],
            ['name' => 'Curtains',            'category' => 'Tents - 10 Span'],
            ['name' => 'Ceilings',            'category' => 'Tents - 10 Span'],

            // ── Tent - G25 ─────────────────────────────────────────────
            ['name' => 'Canvas AA',           'category' => 'Tent - G25'],
            ['name' => 'Canvas BB',           'category' => 'Tent - G25'],
            ['name' => 'Canvas CC',           'category' => 'Tent - G25'],
            ['name' => 'Canvas DD',           'category' => 'Tent - G25'],
            ['name' => 'Sidewalls',           'category' => 'Tent - G25'],
            ['name' => 'Gable (Sambu)',       'category' => 'Tent - G25'],
            ['name' => 'Shade Net',           'category' => 'Tent - G25'],

            // ── Tent - 6x6 ─────────────────────────────────────────────
            ['name' => 'Long Chase',          'category' => 'Tent - 6x6'],
            ['name' => 'Stands',              'category' => 'Tent - 6x6'],
            ['name' => 'Purline',             'category' => 'Tent - 6x6'],
            ['name' => 'Head',                'category' => 'Tent - 6x6'],
            ['name' => 'Short Chase',         'category' => 'Tent - 6x6'],
            ['name' => 'Shado Awning',        'category' => 'Tent - 6x6'],
            ['name' => 'Base Plates',         'category' => 'Tent - 6x6'],
            ['name' => 'Angles',              'category' => 'Tent - 6x6'],
            ['name' => 'Peak',                'category' => 'Tent - 6x6'],
            ['name' => 'Lock',                'category' => 'Tent - 6x6'],
            ['name' => 'Winch',               'category' => 'Tent - 6x6'],
            ['name' => 'Tops',                'category' => 'Tent - 6x6'],
            ['name' => 'Flaps',               'category' => 'Tent - 6x6'],
            ['name' => 'Curtains',            'category' => 'Tent - 6x6'],
            ['name' => 'Ceilings',            'category' => 'Tent - 6x6'],
            ['name' => 'Ceiling Ropes',       'category' => 'Tent - 6x6'],

            // ── Furniture ──────────────────────────────────────────────
            ['name' => 'Banquet Seats',                'category' => 'Furniture'],
            ['name' => 'Rectangular Tables',           'category' => 'Furniture'],
            ['name' => 'Round Tables',                 'category' => 'Furniture'],
            ['name' => 'Round Tables Without Stands',  'category' => 'Furniture'],
            ['name' => 'Executive Round Tables',       'category' => 'Furniture'],
            ['name' => 'Staircases',                   'category' => 'Furniture'],
            ['name' => 'Kids Chairs Blue',             'category' => 'Furniture'],
            ['name' => 'Kids Chairs Pink',             'category' => 'Furniture'],
            ['name' => 'Kids Chairs White',            'category' => 'Furniture'],
            ['name' => 'Executive Seats Red',          'category' => 'Furniture'],
            ['name' => 'Cocktail Tables',              'category' => 'Furniture'],
            ['name' => 'Cocktail Seats',               'category' => 'Furniture'],
            ['name' => 'Cocktail Table Tops',          'category' => 'Furniture'],
            ['name' => 'Brass Stanchions',             'category' => 'Furniture'],
            ['name' => 'Plastic Armless Seats',        'category' => 'Furniture'],

            // ── Flooring ───────────────────────────────────────────────
            ['name' => 'Carpet 4x25',         'category' => 'Flooring'],
            ['name' => 'Carpet 4x20',         'category' => 'Flooring'],
            ['name' => 'Carpet 4x15',         'category' => 'Flooring'],
            ['name' => 'Carpet 6x6',          'category' => 'Flooring'],
            ['name' => 'Carpet 4x6',          'category' => 'Flooring'],
            ['name' => 'Carpet 4x10',         'category' => 'Flooring'],
            ['name' => 'Pro Floor',           'category' => 'Flooring'],
            ['name' => 'Turf Grass',          'category' => 'Flooring'],
            ['name' => 'Walkways',            'category' => 'Flooring'],
            ['name' => 'Carpet Machine',      'category' => 'Flooring'],

            // ── AV Equipment ───────────────────────────────────────────
            ['name' => 'Screens',             'category' => 'AV Equipment'],
            ['name' => 'Stage Boards',        'category' => 'AV Equipment'],
            ['name' => 'Moving Heads',        'category' => 'AV Equipment'],
            ['name' => 'Wide Camera 512dm',   'category' => 'AV Equipment'],
            ['name' => 'Pacans',              'category' => 'AV Equipment'],
            ['name' => 'Strobe Lights',       'category' => 'AV Equipment'],
            ['name' => 'Processor',           'category' => 'AV Equipment'],
            ['name' => 'DJ Equipment',        'category' => 'AV Equipment'],
            ['name' => 'Podium',              'category' => 'AV Equipment'],
            ['name' => 'Big Bee Eye',         'category' => 'AV Equipment'],
            ['name' => 'Small Bee Eye',       'category' => 'AV Equipment'],
            ['name' => 'Fog Machine',         'category' => 'AV Equipment'],
            ['name' => 'Low Fog',             'category' => 'AV Equipment'],
            ['name' => 'Strip Lights',        'category' => 'AV Equipment'],
            ['name' => '200m 3-Face Cable',   'category' => 'AV Equipment'],
            ['name' => 'Scanners',            'category' => 'AV Equipment'],
            ['name' => 'AC Unit',             'category' => 'AV Equipment'],
            ['name' => 'Braces 0.5m',         'category' => 'AV Equipment'],
            ['name' => 'Braces 0.3m',         'category' => 'AV Equipment'],
            ['name' => 'Stand 0.5m',          'category' => 'AV Equipment'],
            ['name' => 'Stand 0.3m',          'category' => 'AV Equipment'],
            ['name' => 'Chain Block',         'category' => 'AV Equipment'],
            ['name' => 'Floodlights',         'category' => 'AV Equipment'],
            ['name' => 'Chandelier',          'category' => 'AV Equipment'],
            ['name' => 'AV Matrix',           'category' => 'AV Equipment'],
            ['name' => 'Laptop',              'category' => 'AV Equipment'],
            ['name' => 'GenSet 110KVA',       'category' => 'AV Equipment'],

            // ── Fabric - Table Cloths ──────────────────────────────────
            ['name' => 'Rectangular Table Cloth Black',  'category' => 'Fabric - Table Cloths'],
            ['name' => 'Rectangular Table Cloth Green',  'category' => 'Fabric - Table Cloths'],
            ['name' => 'Rectangular Table Cloth Red',    'category' => 'Fabric - Table Cloths'],
            ['name' => 'Rectangular Table Cloth White',  'category' => 'Fabric - Table Cloths'],
            ['name' => 'Velvet Round Table Cloth Red',   'category' => 'Fabric - Table Cloths'],
            ['name' => 'Velvet Round Table Cloth Green', 'category' => 'Fabric - Table Cloths'],
            ['name' => 'Velvet Round Table Cloth Black', 'category' => 'Fabric - Table Cloths'],
            ['name' => 'Round Table Cloth Red',          'category' => 'Fabric - Table Cloths'],
            ['name' => 'Round Table Cloth Green',        'category' => 'Fabric - Table Cloths'],
            ['name' => 'Round Table Cloth Black',        'category' => 'Fabric - Table Cloths'],
            ['name' => 'Round Table Cloth White',        'category' => 'Fabric - Table Cloths'],
            ['name' => 'Round Table Cloth Gold',         'category' => 'Fabric - Table Cloths'],
            ['name' => 'Skirting Red',                   'category' => 'Fabric - Table Cloths'],
            ['name' => 'Skirting White',                 'category' => 'Fabric - Table Cloths'],
            ['name' => 'Skirting Green',                 'category' => 'Fabric - Table Cloths'],
            ['name' => 'Spandex White',                  'category' => 'Fabric - Table Cloths'],
            ['name' => 'Spandex Black',                  'category' => 'Fabric - Table Cloths'],
            ['name' => 'Spandex Red',                    'category' => 'Fabric - Table Cloths'],
            ['name' => 'Spandex Green',                  'category' => 'Fabric - Table Cloths'],
            ['name' => 'Underlay / Molton',              'category' => 'Fabric - Table Cloths'],

        ];

        foreach ($inventory as $item) {
            Item::create(array_merge($item, $defaults));
        }
    }
}
