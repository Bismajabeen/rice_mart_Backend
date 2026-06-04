<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestImageController extends Controller
{
    public function upload(Request $request)
    {
        try {

            $request->validate([
                'image' => 'required|image',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Image received successfully',
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}