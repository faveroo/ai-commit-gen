<?php

namespace App\Services;

use App\DTOs\CommitContext;
use App\Factories\AIProviderFactory;
use App\Repositories\ConfigRepository;
use Illuminate\Support\Facades\Http;

class AiService
{
    public function __construct(
        private AIProviderFactory $factory
    ) {
    }
    public function generate(string $prompt): string
    {
        return $this
            ->factory
            ->make()
            ->generate($prompt);
    }
}