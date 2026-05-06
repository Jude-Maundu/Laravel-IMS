<?php

namespace App\Http\Controllers;

use App\Models\ItemPiece;
use App\Models\Item;
use Illuminate\Http\Request;

class LabelController extends Controller
{
    public function single(Request $request, ItemPiece $piece)
    {
        $size = $request->input('size', 'medium');
        $piece->load('item');

        return view('labels.single', [
            'piece' => $piece,
            'size' => $size,
        ]);
    }

    public function byItem(Request $request, Item $item)
    {
        $size = $request->input('size', 'medium');
        $item->load(['pieces' => fn($q) => $q->orderBy('unique_code')]);

        return view('labels.bulk', [
            'pieces' => $item->pieces,
            'title' => $item->name,
            'size' => $size,
        ]);
    }

    public function byCategory(Request $request, string $category)
    {
        $size = $request->input('size', 'medium');

        $items = Item::with(['pieces' => fn($q) => $q->orderBy('unique_code')])
            ->where('category', $category)
            ->orderBy('name')
            ->get();

        $pieces = $items->flatMap->pieces;

        return view('labels.bulk', [
            'pieces' => $pieces,
            'title' => $category,
            'size' => $size,
        ]);
    }
}
