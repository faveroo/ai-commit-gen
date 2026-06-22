<?php

use App\DTOs\ReadmeContext;
use App\Prompts\ReadmePrompt;

it('includes the file listing in the prompt', function () {
    $context = new ReadmeContext(
        files: "app/Commands/CommitCommand.php\napp/Services/AiService.php",
        composer: '{"name": "test/project"}',
        package: null
    );

    $prompt = ReadmePrompt::build($context);

    expect($prompt)->toContain('app/Commands/CommitCommand.php')
        ->and($prompt)->toContain('app/Services/AiService.php');
});

it('includes composer.json content in the prompt', function () {
    $composerContent = '{"name": "faveroo/ai-commit-gen", "description": "AI commit tool"}';

    $context = new ReadmeContext(
        files: 'file.php',
        composer: $composerContent,
        package: null
    );

    $prompt = ReadmePrompt::build($context);

    expect($prompt)->toContain('faveroo/ai-commit-gen');
});

it('handles null package.json gracefully', function () {
    $context = new ReadmeContext(
        files: 'file.php',
        composer: '{}',
        package: null
    );

    $prompt = ReadmePrompt::build($context);

    expect($prompt)->toBeString()
        ->and($prompt)->not->toBeEmpty();
});

it('includes package.json content when present', function () {
    $context = new ReadmeContext(
        files: 'file.php',
        composer: '{}',
        package: '{"name": "my-frontend", "version": "1.0.0"}'
    );

    $prompt = ReadmePrompt::build($context);

    expect($prompt)->toContain('my-frontend');
});

it('includes readme-focused instructions', function () {
    $context = new ReadmeContext(
        files: 'file.php',
        composer: '{}',
        package: null
    );

    $prompt = ReadmePrompt::build($context);

    expect($prompt)->toContain('Installation')
        ->and($prompt)->toContain('features')
        ->and($prompt)->toContain('Return only markdown');
});
