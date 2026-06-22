<?php

namespace App\AI\Providers;

use App\AI\Contracts\AIProvider;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OllamaProvider implements AIProvider
{
    public function __construct(
        protected string $model,
    ) {}

    public function generate(string $prompt): string
    {
        $response = Http::timeout(360)
            ->retry(2, 1000)
            ->post(
                'http://localhost:11434/api/generate',
                [
                    'model' => $this->model,
                    'prompt' => $prompt,
                    'stream' => false,
                ]
            );

        if ($response->failed()) {
            throw new RuntimeException(
                "Ollama error [{$response->status()}]: " . $response->body()
            );
        }

        $text = data_get($response->json(), 'response');

        if (blank($text)) {
            throw new RuntimeException(
                "Ollama returned an empty response."
            );
        }

        return data_get(
            $response->json(),
            'response'
        );
    }
}
