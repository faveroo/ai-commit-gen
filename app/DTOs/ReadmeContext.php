<?php

namespace App\DTOs;

class ReadmeContext
{
    public function __construct(
        public string $files,
        public ?string $composer,
        public ?string $package
    ) {}
}