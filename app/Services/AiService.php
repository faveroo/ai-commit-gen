<?php

namespace App\Services;

use App\AI\Contracts\AIProvider;

class AiService
{
    public function __construct(
        private AIProvider $provider
    ) {}
    public function generate(string $prompt): string
    {
        return $this
            ->provider
            ->generate($prompt);
    }
}
