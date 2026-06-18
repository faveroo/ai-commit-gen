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

    public function generateExplanation(string $diff): string
    {
        $prompt = <<<PROMPT
You are a senior software engineer performing a code review.

Analyze the provided git diff and explain:

1. What was changed.
2. What was added, removed, or modified.
3. The likely purpose of the change.
4. Potential impacts on the application.
5. Any risks, edge cases, or considerations.

Rules:
- Be concise and objective.
- Focus only on the provided diff.
- Do not speculate beyond what the diff reasonably indicates.
- Use technical language suitable for developers.
- Organize the response using Markdown.
- If tests were added, mention them.
- If new files were added, mention them.
- If database changes were detected, explain their impact.
- If API changes were detected, explain their impact.

Return the response in the following format:

## Summary

A short summary of the change.

## Changes

- Item 1
- Item 2
- Item 3

## Purpose

Explanation of why this change was likely made.

## Impact

Explanation of how the application may be affected.

## Risks

Any potential concerns, risks, or areas to validate.

Git Diff:

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