<?php

use App\DTOs\CommitContext;
use App\Prompts\ExplainPrompt;

it('includes the diff in the explain prompt', function () {
    $context = new CommitContext(
        branch: 'main',
        files: 'M app/Example.php',
        diff: '+new code line here'
    );

    $prompt = ExplainPrompt::build($context);

    expect($prompt)->toContain('+new code line here');
});

it('asks for json response', function () {
    $context = new CommitContext(
        branch: 'main',
        files: 'M file.php',
        diff: '+change'
    );

    $prompt = ExplainPrompt::build($context);

    expect($prompt)->toContain('Return ONLY valid JSON');
});

it('includes review categories', function () {
    $context = new CommitContext(
        branch: 'main',
        files: 'M file.php',
        diff: '+change'
    );

    $prompt = ExplainPrompt::build($context);

    expect($prompt)->toContain('Bugs')
        ->and($prompt)->toContain('Security vulnerabilities')
        ->and($prompt)->toContain('Performance issues')
        ->and($prompt)->toContain('Code smells');
});

it('returns a non-empty string', function () {
    $context = new CommitContext(
        branch: 'main',
        files: 'M file.php',
        diff: '+something'
    );

    $prompt = ExplainPrompt::build($context);

    expect($prompt)->toBeString()
        ->and($prompt)->not->toBeEmpty();
});
