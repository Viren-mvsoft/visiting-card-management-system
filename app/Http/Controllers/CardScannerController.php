<?php

namespace App\Http\Controllers;

use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CardScannerController extends Controller
{
    protected GeminiService $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    /**
     * Scan business card images/PDF and return extracted JSON.
     */
    public function scan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'files' => 'required|array|min:1|max:3',
            'files.*' => 'required|file|mimes:jpeg,png,webp,pdf|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid files. Please upload up to 3 images or a PDF.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $files = $request->file('files');
            $data = $this->gemini->scanBusinessCard($files);

            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not extract information from the card. The images may be too blurry or the content is not a business card.'
                ], 422);
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            \Log::error('Scan error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during scanning: ' . $e->getMessage()
            ], 500);
        }
    }
}
