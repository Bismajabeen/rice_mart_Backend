<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
class RecommendationController extends Controller
{
    public function recommend(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:200',
        ]);
        $query = $request->input('query');

        // ── 1. Fetch products ──────────────────────────────────
        $allProducts = Product::with(['shop', 'riceCategory'])
            ->where('stock', '>', 0)
            ->get()
            ->map(function ($p) {
                return [
                    'id'            => $p->id,
                    'name'          => $p->name,
                    'price'         => $p->price,
                    'stock'         => $p->stock,
                    'shop_name'     => optional($p->shop)->shop_name ?? 'N/A',
                    'category_name' => optional($p->riceCategory)->name ?? 'N/A',
                ];
            })
            ->toArray();

        $productsJson = json_encode($allProducts);

        // ── 2. Build prompt ────────────────────────────────────
        $systemPrompt = <<<PROMPT
You are an expert rice advisor for a rice shop called "Rice Mart".
Your job is to help customers understand different rice types and dishes.

When given a query about a rice type or dish, respond ONLY with a valid JSON object
(no markdown, no extra text) with this exact structure:

{
  "overview": "Brief description of this rice or dish",
  "rice_type": "The rice variety name",
  "origin": "Country/region of origin",
  "grain_size": "Short / Medium / Long",
  "texture": "Sticky / Fluffy / Firm etc.",
  "best_uses": ["use 1", "use 2", "use 3"],
  "nutrition": "Brief nutritional info",
  "storage_tips": "How to store this rice properly",
  "recipe": {
    "name": "A popular recipe using this rice",
    "ingredients": ["ingredient 1", "ingredient 2"],
    "steps": ["step 1", "step 2", "step 3"]
  },
  "related_keywords": ["keyword1", "keyword2"]
}
Be accurate, helpful, and keep responses in a friendly tone.
PROMPT;

        $userMessage = <<<MSG
Query: "$query"

Here are the products available in our shop (JSON):
$productsJson

Provide detailed rice information for the query.
Also identify which products match this rice type or dish.
MSG;

        // ── 3. Call OpenAI ─────────────────────────────────────
        try {
            $response = Http::withToken(config('services.openai.key'))
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model'       => 'gpt-4o-mini',   // ← gpt-3.5-turbo is deprecated
                    'messages'    => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user',   'content' => $userMessage],
                    ],
                    'max_tokens'  => 1200,
                    'temperature' => 0.7,
                ]);

            // Log full OpenAI response for debugging
            \Log::info('OpenAI status: ' . $response->status());
            \Log::info('OpenAI body: ' . $response->body());

            if (!$response->successful()) {
                return response()->json([
                    'error' => 'OpenAI API error: ' . $response->body()
                ], 500);
            }
            $content = $response->json()['choices'][0]['message']['content'] ?? '{}';

            // Strip markdown fences if present
            $content = preg_replace('/```json|```/', '', $content);
            $content = trim($content);

            $aiData = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                \Log::error('JSON decode error. Raw content: ' . $content);
                return response()->json(['error' => 'Invalid AI response format'], 500);
            }

            // ── 4. Match products ──────────────────────────────
            $keywords = array_merge(
                [$query],
                $aiData['related_keywords'] ?? [],
                [$aiData['rice_type']       ?? '']
            );

            $matchedProducts = $this->matchProducts($allProducts, $keywords);

            return response()->json([
                'ai'       => $aiData,
                'products' => $matchedProducts,
            ]);

        } catch (\Exception $e) {
            \Log::error('AI Recommendation Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Request failed: ' . $e->getMessage()], 500);
        }
    }

    // =========================================================
    // MATCH PRODUCTS BY KEYWORDS
    // =========================================================
    private function matchProducts(array $products, array $keywords): array
    {
        $matched = [];

        foreach ($products as $product) {
            $productName  = strtolower($product['name']);
            $categoryName = strtolower($product['category_name']);

            foreach ($keywords as $keyword) {
                $kw = strtolower(trim($keyword));
                if (empty($kw)) continue;

                if (
                    str_contains($productName,  $kw) ||
                    str_contains($categoryName, $kw) ||
                    str_contains($kw, $productName)
                ) {
                    $matched[] = $product;
                    break;
                }
            }
             }

        return array_values($matched);
    }
}
