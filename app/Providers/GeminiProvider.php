<?php

namespace App\Providers;

use App\Contracts\AIProvider;
use Illuminate\Support\Facades\Http;

class GeminiProvider implements AIProvider
{
    public function __construct(
        protected string $apiKey,
        protected string $model,
    ) {}

    public function generate(string $prompt): string
    {
        $response = Http::post(
            sprintf(
                'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
                $this->model,
                $this->apiKey
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

        return data_get(
            $response->json(),
            'candidates.0.content.parts.0.text'
        );
    }
}