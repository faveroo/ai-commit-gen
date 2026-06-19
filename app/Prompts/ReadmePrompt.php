<?php

namespace App\Prompts;

use App\Commands\ReadmeCommand;
use App\DTOs\ReadmeContext;

class ReadmePrompt
{
    public static function build(
        ReadmeContext $context
    ): string
    {
        return <<<PROMPT
You are a senior developer advocate.

Generate a README for an end-user.

Focus on:

- What problem the project solves
- Main features
- Installation
- Usage examples
- Configuration

Do not document Laravel Zero.

Do not explain internal implementation.

Assume users want to install and use the tool.

Repository files:

{$context->files}

Composer.json:

{$context->composer}

Package.json:

{$context->package}

Return only markdown.
PROMPT;
PROMPT;
    }
}