<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
class TestImageController extends Controller
{
    public function upload(Request $request)
    {
        try {

            $request->validate([
                'image' => 'required|image',
            ]);
              // ── 2. Image → Base64 ────────────────────────────────
        $imageFile = $request->file('image');
        $imageData = base64_encode(file_get_contents($imageFile->getRealPath()));
        $mimeType  = $imageFile->getMimeType(); // image/jpeg etc.

        // ── 3. Prompt ─────────────────────────────────────────
        $prompt = 'You are an agricultural rice quality inspector.
Analyze the uploaded image and return ONLY valid JSON.
Instructions:
1. Determine whether the image contains rice grains.
2. If it contains rice, identify the most likely rice variety.
3. Evaluate the visible quality of the rice.
4. Do not guess when confidence is low.
5. Base your assessment only on what is visible in the image.
6. Explain any uncertainty.
Return this exact JSON structure:
{
  "is_rice": true,
  "confidence": 0,
  "rice_type": "",
  "rice_type_confidence": 0,
  "quality": "good|average|poor|unknown",
  "quality_score": 0,
  "observations": [""],
  "defects": [""],
  "reasoning": "",
  "recommendation": ""
}';

        // ── 4. OpenAI API Call ────────────────────────────────
        $response = Http::withToken(env('OPENAI_API_KEY'))
            ->timeout(60)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model'    => 'gpt-4o',
                'messages' => [
                    [
                        'role'    => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => $prompt,
                            ],
                            [
                                'type'      => 'image_url',
                                'image_url' => [
                                    'url' => "data:{$mimeType};base64,{$imageData}",
                                ],
                            ],
                        ],
                    ],
                ],
                'max_tokens' => 800,
            ]);

        // ── 5. Parse Response ─────────────────────────────────
        if ($response->failed()) {
            return response()->json([
                'success' => false,
                'message' => 'OpenAI API error: ' . $response->body(),
            ], 500);
        }

        $content = $response->json('choices.0.message.content');

        // Clean markdown fences if any
        $content = preg_replace('/```json|```/', '', $content);
        $content = trim($content);

        $result = json_decode($content, true);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to parse AI response',
            ], 500);
        }
 // ── 7. Return ─────────────────────────────────────────
        return response()->json([
            'success' => true,
            'data'    => $result,
        ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
