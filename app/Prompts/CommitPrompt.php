<?php

namespace App\Prompts;

use App\DTOs\CommitContext;

class CommitPrompt
{
    public static function build(
        CommitContext $context
    ): string {
        return <<<PROMPT
You are a senior software engineer.

Analyze the git diff and generate a Conventional Commit.

Rules:

- Return ONLY the commit message
- No markdown
- No explanations
- Maximum 200 characters
- Use Conventional Commits

Valid types:

feat
fix
docs
refactor
test
chore
build
perf

type: message

Current branch:

{$context->branch}

Changed Files:

{$context->files}

Git diff:

{$context->diff}
PROMPT;
    }
}