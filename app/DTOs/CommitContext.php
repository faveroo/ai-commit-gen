<?php

namespace App\DTOs;

class CommitContext
{
    public function __construct(
        public string $branch,
        public string $files,
        public string $diff
    ) {}
}