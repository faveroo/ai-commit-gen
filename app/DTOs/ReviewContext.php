<?php

namespace App\DTOs;

class ReviewContext
{
    public function __construct
    (
        public string $summary,
        public string $risk,
        public array $issues,
        public array $recommendations
    ) {}
}