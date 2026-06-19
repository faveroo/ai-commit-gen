<?php

namespace App\Factories;

use App\Contracts\AIProvider;
use App\Providers\GeminiProvider;
use App\Providers\OllamaProvider;
use App\Repositories\ConfigRepository;

class AIProviderFactory
{
    public function __construct(
        protected ConfigRepository $config
    ) {}

    public function make(): AIProvider
    {
        return match (
            $this->config->get('provider')
        ) {
            'ollama' => new OllamaProvider(
                $this->config->get('model') ?? 'qwen3:8b'
            ),

            default => new GeminiProvider(
                $this->config->get('api-key'),
                $this->config->get('model')
                    ?? 'gemini-2.5-flash'
            ),
        };
    }
}