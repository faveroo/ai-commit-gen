<?php

namespace App\Contracts;

interface AIProvider
{
    public function generate(string $prompt): string;
}