<?php

namespace App\Prompts;

use App\DTOs\CommitContext;

class CommitPrompt
{
    public static function build(
        CommitContext $context
    ): string {
        return <<<PROMPT
You are a senior software engineer specialized in Git and Conventional Commits.

Your task is to analyze the staged git changes and generate ONE Conventional Commit message.

Return ONLY the commit message.

Do NOT return:

* Markdown
* Quotes
* Code blocks
* Explanations
* Multiple commit suggestions

Conventional Commit rules:

feat:

* New functionality
* New endpoints
* New commands
* New user-facing capabilities

fix:

* Bug fixes
* Incorrect behavior
* Error handling improvements

refactor:

* Internal code changes without changing behavior
* Architecture improvements
* Code organization

perf:

* Performance improvements

test:

* Adding or updating tests

docs:

* Documentation only

build:

* Build, dependencies, CI/CD

chore:

* Maintenance tasks
* Configuration changes
* Tooling changes

Scope rules:

- Derive the scope from the changed files.
- Use the most affected domain/module.
- Do not invent unrelated scopes.
- If no clear scope exists, omit the scope.

Examples:

app/Services/AiService.php -> ai
app/Commands/CommitCommand.php -> commit
app/Providers/OllamaProvider.php -> ai
app/Repositories/UserRepository.php -> user

Analysis process:

1. Identify the primary purpose of the change.
2. Determine whether behavior changed.
3. Determine whether a new capability was introduced.
4. Choose the most appropriate Conventional Commit type.
5. Generate a concise commit message.

Prioritize the actual code changes over filenames.

Format:

type(scope): short description

Examples:

feat(auth): add password reset support
fix(api): handle missing user token
refactor(ai): extract provider factory
chore(config): update default model settings

Current branch:

{$context->branch}

Changed files:

{$context->files}

Git diff:

{$context->diff}
PROMPT;
    }
}