<?php

namespace App\DTOs;

class DiffContext extends BaseContext
{
    public function __construct(
        public string $branch,
        public string $files,
        public string $diff
    ) {}
}
