<?php

use App\DTOs\CommitContext;
use App\Prompts\ReviewPrompt;

it('includes the diff in the review prompt', function () {
    $context = new CommitContext(
        branch: 'main',
        files: 'M app/Example.php',
        diff: '+public function newFeature() { return true; }'
    );

    $prompt = ReviewPrompt::build($context);

    expect($prompt)->toContain('+public function newFeature() { return true; }');
});

it('includes the json schema in the prompt', function () {
    $context = new CommitContext(
        branch: 'main',
        files: 'M file.php',
        diff: '+change'
    );

    $prompt = ReviewPrompt::build($context);

    expect($prompt)->toContain('"summary"')
        ->and($prompt)->toContain('"risk"')
        ->and($prompt)->toContain('"issues"')
        ->and($prompt)->toContain('"recommendations"');
});

it('instructs the model to return valid json', function () {
    $context = new CommitContext(
        branch: 'main',
        files: 'M file.php',
        diff: '+change'
    );

    $prompt = ReviewPrompt::build($context);

    expect($prompt)->toContain('valid JSON object');
});

it('returns a non-empty string', function () {
    $context = new CommitContext(
        branch: 'main',
        files: 'M file.php',
        diff: '+something'
    );

    $prompt = ReviewPrompt::build($context);

    expect($prompt)->toBeString()
        ->and($prompt)->not->toBeEmpty();
});
