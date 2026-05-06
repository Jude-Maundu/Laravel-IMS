<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        // Sync categories from items table to categories table
        $itemCategories = \App\Models\Item::distinct()->whereNotNull('category')->pluck('category');
        foreach ($itemCategories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }

        $categories = Category::orderBy('name')->get();
        return view('inventory.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create($validated);

        return back()->with('success', 'Category "' . $validated['name'] . '" created successfully.');
    }

    public function destroy(Category $category)
    {
        $categoryName = $category->name;
        $category->delete();
        return back()->with('warning', 'Category "' . $categoryName . '" deleted permanently.');
    }
}
