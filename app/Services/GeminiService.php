<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
        // Using the user-provided stable alias
        $this->model = config('services.gemini.model', 'gemini-flash-latest');
    }

    /**
     * Scan one or more business card files and extract contact information.
     *
     * @param array $files Array of uploaded file objects or base64 data
     * @return array|null
     */
    public function scanBusinessCard(array $files): ?array
    {
        try {
            $contents = [];
            
            // 1. Add the text prompt
            $contents[] = [
                'text' => "Act as a specialized business card OCR and data extraction expert. 
                I will provide one or more images (front and possibly back) or a PDF of a business card.
                
                Your task:
                1. Extract all relevant contact information.
                2. If multiple images are provided, combine the data (e.g., front has name, back has address).
                3. Clean up the data (proper capitalization, remove extra spaces).
                4. Return ONLY a valid JSON object with the following structure:
                {
                   \"name\": \"Full Name\",
                   \"company\": \"Company Name\",
                   \"website\": \"Website URL\",
                   \"address\": \"Physical Address\",
                   \"country\": \"Standard Country Name (e.g. India, United States)\",
                   \"emails\": [\"email1@example.com\", \"email2@example.com\"],
                   \"phones\": [\"+1234567890\", \"+0987654321\"],
                   \"notes\": \"Any other relevant info like job title, etc.\"
                }
                
                Important: Return ONLY the raw JSON. Do not include markdown code blocks or any other text."
            ];

            // 2. Add files
            foreach ($files as $file) {
                if (is_string($file)) {
                    // Assume base64 string
                    $contents[] = [
                        'inlineData' => [
                            'mimeType' => 'image/jpeg',
                            'data' => $file
                        ]
                    ];
                } else {
                    $contents[] = [
                        'inlineData' => [
                            'mimeType' => $file->getMimeType(),
                            'data' => base64_encode(file_get_contents($file->getPathname()))
                        ]
                    ];
                }
            }

            Log::info('Sending request to Gemini model: ' . $this->model);

            $response = Http::withOptions([
                'verify' => false,
            ])->withHeaders([
                'Content-Type' => 'application/json',
                'X-goog-api-key' => $this->apiKey,
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent", [
                'contents' => [
                    [
                        'parts' => $contents
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.1,
                    'topK' => 1,
                    'topP' => 1,
                    'maxOutputTokens' => 2048,
                ]
            ]);

            Log::info('Gemini API Response Status: ' . $response->status());

            if ($response->failed()) {
                Log::error('Gemini API Error (' . $response->status() . '): ' . $response->body());
                return null;
            }

            $result = $response->json();
            $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (!$text) {
                Log::warning('Gemini API returned no text in candidates. Response: ' . json_encode($result));
                return null;
            }

            // Clean up any potential markdown formatting
            $jsonString = trim($text);
            if (strpos($jsonString, '```json') === 0) {
                $jsonString = substr($jsonString, 7, -3);
            } elseif (strpos($jsonString, '```') === 0) {
                $jsonString = substr($jsonString, 3, -3);
            }

            return json_decode(trim($jsonString), true);

        } catch (\Exception $e) {
            Log::error('Gemini Service Exception: ' . $e->getMessage());
            return null;
        }
    }
}
