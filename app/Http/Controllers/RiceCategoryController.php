<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RiceCategory;
use Illuminate\Support\Facades\Storage;

class RiceCategoryController extends Controller
{
    // =========================
    // FETCH ACTIVE CATEGORIES
    // =========================
    public function index()
    {
        $categories = RiceCategory::where('status', true)
            ->latest()
            ->get();

        return response()->json($categories);
    }

    // =========================
    // FETCH ALL CATEGORIES
    // ADMIN PURPOSE
    // =========================
    public function allCategories()
    {
        return response()->json(
            RiceCategory::latest()->get()
        );
    }

    // =========================
    // CREATE CATEGORY
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'image'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'nullable|boolean',
        ]);

        $path = null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
        }

        $category = RiceCategory::create([
            'name'   => $request->name,
            'image'  => $path,
            'status' => $request->boolean('status', true),
        ]);

        return response()->json([
            'message'  => 'Category created',
            'category' => $category,
        ], 201);
    }

    // =========================
    // UPDATE CATEGORY (name/image)
    // =========================
    public function update(Request $request, $id)
    {
        $category = RiceCategory::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }

        $request->validate([
            'name'  => 'sometimes|required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [];

        if ($request->filled('name')) {
            $data['name'] = $request->name;
        }

        if ($request->hasFile('image')) {
            // delete old image before saving new one
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return response()->json([
            'message'  => 'Category updated',
            'category' => $category,
        ]);
    }

    // =========================
    // UPDATE CATEGORY STATUS
    // =========================
    public function updateStatus(Request $request, $id)
    {
        $category = RiceCategory::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }

        $request->validate([
            'status' => 'required|boolean'
        ]);

        $category->update([
            'status' => $request->status
        ]);

        return response()->json([
            'message'  => 'Category status updated',
            'category' => $category
        ]);
    }
}