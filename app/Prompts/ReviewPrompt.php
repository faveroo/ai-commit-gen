<?php

namespace App\Prompts;

use App\DTOs\CommitContext;

class ReviewPrompt
{
    public static function build(
        CommitContext $context
    ): string {
        return <<<PROMPT
You are a senior software engineer performing a code review.

1. What was changed.
2. What was added, removed, or modified.
3. The likely purpose of the change.
4. Potential impacts on the application.
5. Any risks, edge cases, or considerations.

Analyze the provided git diff and return your response as a **valid JSON object** following exactly this schema:

```json
{
  "summary": "string",
  "risk": "string",
  "issues": [
    {
      "severity": "low|medium|high",
      "title": "string",
      "category": "string"
    }
  ],
  "recommendations": [
    {
      "priority": "low|medium|high",
      "title": "string",
      "description": "string"
    }
  ]
}
```

Requirements:

* `summary` and `risks` must be strings.
* `issues` must always be an array. Return an empty array (`[]`) when no issues are found.
* `recommendations` must always be an array. Return an empty array (`[]`) when no recommendations are found.
* `Risks` must always be a string. Return 'None' when no risks are found.
* Do not return `null` for any field.
* Do not omit fields.
* Do not include markdown, code fences, explanations, or additional text outside the JSON object.
* Focus only on the provided diff.
* Be concise and objective.
* Do not speculate beyond what the diff reasonably indicates.
* If database changes were detected, explain their impact.
* If API changes were detected, explain their impact.

Git Diff:

{$context->diff}

PROMPT;
    }
}