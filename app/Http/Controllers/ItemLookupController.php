<?php

namespace App\Http\Controllers;

use App\Models\ItemPiece;
use Illuminate\Http\Request;

class ItemLookupController extends Controller
{
    public function show(string $unique_code)
    {
        $piece = ItemPiece::with('item')->where('unique_code', $unique_code)->first();

        if (!$piece) {
            return view('items.not-found', ['code' => $unique_code]);
        }

        // If user is authenticated, redirect to item detail with piece highlight
        if (auth()->check()) {
            return redirect()->route('inventory.show', ['id' => $piece->item_id, 'piece' => $unique_code]);
        }

        // Public lookup view
        return view('items.public-lookup', ['piece' => $piece]);
    }
}
