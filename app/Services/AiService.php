<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AiService
{
    public function generateCommit(string $diff, string $status): string
    {
        $prompt = <<<PROMPT
You are a senior software engineer.

Analyze the git diff and generate a Conventional Commit.

Rules:

- Return ONLY the commit message
- No markdown
- No explanations
- Maximum 72 characters
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

Git status:

{$status}

Git diff:

{$diff}
PROMPT;

    $response = Http::post(
            sprintf(
                'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
                config('ai.gemini.model'),
                config('ai.gemini.key')
            ),
            [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt,
                            ],
                        ],
                    ],
                ],
            ]
        );

    return trim(
        data_get(
            $response->json(),
            'candidates.0.content.parts.0.text'
        )
    );
    }
}