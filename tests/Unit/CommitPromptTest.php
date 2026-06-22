<?php

use App\DTOs\CommitContext;
use App\Prompts\CommitPrompt;

it('includes the branch name in the prompt', function () {
    $context = new CommitContext(
        branch: 'feature/user-auth',
        files: 'M app/Services/AuthService.php',
        diff: '+public function login() {}'
    );

    $prompt = CommitPrompt::build($context);

    expect($prompt)->toContain('feature/user-auth');
});

it('includes the changed files in the prompt', function () {
    $context = new CommitContext(
        branch: 'main',
        files: "M app/Services/AuthService.php\nA app/DTOs/LoginRequest.php",
        diff: '+some diff content'
    );

    $prompt = CommitPrompt::build($context);

    expect($prompt)->toContain('M app/Services/AuthService.php')
        ->and($prompt)->toContain('A app/DTOs/LoginRequest.php');
});

it('includes the diff in the prompt', function () {
    $diff = <<<'DIFF'
-    public function oldMethod()
+    public function newMethod()
DIFF;

    $context = new CommitContext(
        branch: 'main',
        files: 'M app/Example.php',
        diff: $diff
    );

    $prompt = CommitPrompt::build($context);

    expect($prompt)->toContain('oldMethod')
        ->and($prompt)->toContain('newMethod');
});

it('includes conventional commit instructions', function () {
    $context = new CommitContext(
        branch: 'main',
        files: 'M file.php',
        diff: '+change'
    );

    $prompt = CommitPrompt::build($context);

    expect($prompt)->toContain('Conventional Commit')
        ->and($prompt)->toContain('feat:')
        ->and($prompt)->toContain('fix:')
        ->and($prompt)->toContain('refactor:')
        ->and($prompt)->toContain('type(scope): short description');
});

it('returns a non-empty string', function () {
    $context = new CommitContext(
        branch: 'develop',
        files: 'A new-file.php',
        diff: '+new content'
    );

    $prompt = CommitPrompt::build($context);

    expect($prompt)->toBeString()
        ->and($prompt)->not->toBeEmpty();
});
