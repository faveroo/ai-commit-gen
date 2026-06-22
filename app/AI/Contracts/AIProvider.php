<?php

namespace App\AI\Contracts;

interface AIProvider
{
    public function generate(string $prompt): string;
}
