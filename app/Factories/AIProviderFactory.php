<?php

namespace App\Factories;

use App\Contracts\AIProvider;
use App\Providers\GeminiProvider;
use App\Providers\OllamaProvider;
use App\Repositories\ConfigRepository;
use RuntimeException;

class AIProviderFactory
{
    public function __construct(
        protected ConfigRepository $config
    ) {}

    public function make(): AIProvider
    {
        $provider = $this->config->get('provider');

        if (! $provider) {
            throw new RuntimeException(
                'No provider configured. Run: ai-commit config:set provider {Ai Provider}'
            );
        }

        return match ($this->config->get('provider')) {
            'ollama' => $this->makeOllama(),
            'gemini' => $this->makeGemini(),
            default => throw new RuntimeException(
                "Unknown provider [{$provider}]"
            ),
        };
    }

    public function makeGemini()
    {
        $key = env('GEMINI_API_KEY');

        if (! $key) {
            throw new RuntimeException(
                'Gemini API key not configured. Run: ai-commit config:set api-key {Api key}'
            );
        }

        $model = $this->config->get('model');

        if (! $model) {
            throw new RuntimeException(
                'Gemini API model not configured. Run: ai-commit config:set model {Model name}'
            );
        }

        return new GeminiProvider(
            key: $key,
            model: $model,
        );
    }

    public function makeOllama()
    {
        $model = $this->config->get('model');

        if (! $model) {
            throw new RuntimeException(
                'Ollama model not configured. Run: ai-commit config:set model {Model name}'
            );
        }

        return new OllamaProvider(
            model: $model,
        );
    }
}
