<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RiceCategory;
use App\Models\RiceDetectionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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
        $fullPath = storage_path('app/public/' . $path);

        try {
            $mlServiceUrl = config('services.rice_ml.url', 'http://127.0.0.1:5000/predict');

            $response = Http::attach(
                'image',
                file_get_contents($fullPath),
                basename($fullPath)
            )->post($mlServiceUrl);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'ML service failed to classify the image.',
                ], 502);
            }

            $mlResult = $response->json();
            $label = $mlResult['label'] ?? null;
            $confidence = $mlResult['confidence'] ?? 0;

            $category = RiceCategory::where('model_label', $label)->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category could not be matched. Please try another image.',
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
}