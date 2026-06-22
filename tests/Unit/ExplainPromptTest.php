<?php

use App\DTOs\DiffContext;
use App\Prompts\ExplainPrompt;

it('includes the diff in the explain prompt', function () {
    $context = new DiffContext(
        branch: 'main',
        files: 'M app/Example.php',
        diff: '+new code line here'
    );

    $prompt = ExplainPrompt::build($context);

    expect($prompt)->toContain('+new code line here');
});

it('asks for json response', function () {
    $context = new DiffContext(
        branch: 'main',
        files: 'M file.php',
        diff: '+change'
    );

    $prompt = ExplainPrompt::build($context);

    expect($prompt)->toContain('Return ONLY valid JSON');
});

it('includes explanation categories', function () {
    $context = new DiffContext(
        branch: 'main',
        files: 'M file.php',
        diff: '+change'
    );

    $prompt = ExplainPrompt::build($context);

    expect($prompt)->toContain('what_changed')
        ->and($prompt)->toContain('why_it_matters')
        ->and($prompt)->toContain('potential_impacts')
        ->and($prompt)->toContain('behavioral_changes');
});

it('returns a non-empty string', function () {
    $context = new DiffContext(
        branch: 'main',
        files: 'M file.php',
        diff: '+something'
    );

    $prompt = ExplainPrompt::build($context);

    expect($prompt)->toBeString()
        ->and($prompt)->not->toBeEmpty();
});
