<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Category::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string'
        ]);

        $category = Category::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description
            ]);
        }

        return back()->with('success', 'Category created successfully.');
    }
}
