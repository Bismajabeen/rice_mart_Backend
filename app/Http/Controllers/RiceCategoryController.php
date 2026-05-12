<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RiceCategory;

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

            'message' => 'Category status updated',

            'category' => $category
        ]);
    }
}