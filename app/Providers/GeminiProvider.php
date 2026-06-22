<?php

namespace App\Providers;

use App\Contracts\AIProvider;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiProvider implements AIProvider
{
    public function __construct(
        protected string $key,
        protected string $model,
    ) {}

    public function generate(string $prompt): string
    {
        $response = Http::timeout(120)
            ->retry(2, 1000)
            ->post(
                sprintf(
                    'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
                    $this->model,
                    $this->key
                ),
                [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => $prompt,
                                ]
                            ]
                        ]
                    ]
                ]
            );

        if ($response->failed()) {
            throw new RuntimeException(
                "Gemini API error [{$response->status()}]: " . $response->body()
            );
        }

        $text = data_get($response->json(), 'candidates.0.content.parts.0.text');

        if (blank($text)) {
            throw new RuntimeException('Gemini returned an empty response');
        }

        return $text;
    }
}
