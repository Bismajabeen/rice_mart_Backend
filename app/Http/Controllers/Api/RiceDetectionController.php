<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RiceCategory;
use App\Models\RiceDetectionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RiceDetectionController extends Controller
{
    public function detect(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $start = microtime(true);

        $path = $request->file('image')->store('rice_uploads', 'public');

        try {
            // ───── TEMPORARY MOCK (real AI model abhi tak nahi laga) ─────
            $mockLabels = ['basmati', 'parboiled', 'sella', 'brown', 'sticky'];
            $label = $mockLabels[array_rand($mockLabels)];
            $confidence = round(mt_rand(85, 99) / 100, 2);
            // ──────────────────────────────────────────────────────────

            $category = RiceCategory::where('model_label', $label)->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category could not be matched.',
                ], 404);
            }

            $processingTimeMs = (int) ((microtime(true) - $start) * 1000);

            RiceDetectionLog::create([
                'user_id' => $request->user()?->id,
                'rice_category_id' => $category->id,
                'image_path' => $path,
                'confidence' => $confidence,
                'processing_time_ms' => $processingTimeMs,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'category' => $category->name,
                    'confidence' => $confidence,
                    'cooking_time' => $category->cooking_time,
                    'common_uses' => $category->common_uses,
                    'description' => $category->description,
                    'processing_time_ms' => $processingTimeMs,
                    'image_url' => Storage::url($path),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Detection failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function history(Request $request)
    {
        $logs = RiceDetectionLog::with('category')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }

    /**
     * GET /api/rice/recommend?use_case=biryani
     * Recommends the best-matching rice category based on the
     * user's selected use-case (biryani, daily, diet, dessert),
     * matched against the 'common_uses' text field.
     */
    public function recommend(Request $request)
    {
        $request->validate([
            'use_case' => 'required|string',
        ]);

        $useCase = strtolower($request->input('use_case'));

        // Map friendly use-case keys to keywords found in common_uses column
        $keywordMap = [
            'biryani' => 'Biryani',
            'daily'   => 'Daily cooking',
            'diet'    => 'diet',
            'dessert' => 'Dessert',
        ];

        $keyword = $keywordMap[$useCase] ?? $useCase;

        $matches = RiceCategory::where('common_uses', 'like', "%{$keyword}%")
            ->orWhere('description', 'like', "%{$keyword}%")
            ->get();

        if ($matches->isEmpty()) {
            // Fallback: recommend the most commonly used all-rounder (Basmati)
            $matches = RiceCategory::where('model_label', 'basmati')->get();
        }

        return response()->json([
            'success' => true,
            'data' => $matches->map(function ($cat) {
                return [
                    'category' => $cat->name,
                    'cooking_time' => $cat->cooking_time,
                    'common_uses' => $cat->common_uses,
                    'description' => $cat->description,
                ];
            }),
        ]);
    }
}