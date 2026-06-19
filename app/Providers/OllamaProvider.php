<?php

namespace App\Providers;

use App\Contracts\AIProvider;
use Illuminate\Support\Facades\Http;

class OllamaProvider implements AIProvider
{
    public function __construct(
        protected string $model,
    ) {}

    public function generate(string $prompt): string
    {
        $response = Http::post(
            'http://localhost:11434/api/generate',
            [
                'model' => $this->model,
                'prompt' => $prompt,
                'stream' => false,
            ]
        );
        // print($prompt);
        // dd($response->json());

        return data_get(
            $response->json(),
            'response'
        );
    }
}