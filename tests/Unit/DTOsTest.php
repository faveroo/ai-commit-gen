<?php

use App\DTOs\CommitContext;
use App\DTOs\ReadmeContext;

describe('CommitContext', function () {
    it('stores branch, files, and diff', function () {
        $context = new CommitContext(
            branch: 'feature/test',
            files: 'M file.php',
            diff: '+new line'
        );

        expect($context->branch)->toBe('feature/test')
            ->and($context->files)->toBe('M file.php')
            ->and($context->diff)->toBe('+new line');
    });

    it('handles empty strings', function () {
        $context = new CommitContext(
            branch: '',
            files: '',
            diff: ''
        );

        expect($context->branch)->toBe('')
            ->and($context->files)->toBe('')
            ->and($context->diff)->toBe('');
    });

    it('handles multiline diff content', function () {
        $diff = <<<'DIFF'
--- a/file.php
+++ b/file.php
@@ -1,3 +1,4 @@
 <?php
+use App\Service;
 class Example {}
DIFF;

        $context = new CommitContext(
            branch: 'main',
            files: 'M file.php',
            diff: $diff
        );

        expect($context->diff)->toContain('use App\Service');
    });
});

describe('ReadmeContext', function () {
    it('stores files, composer, and package', function () {
        $context = new ReadmeContext(
            files: 'file1.php',
            composer: '{"name": "test"}',
            package: '{"name": "frontend"}'
        );

        expect($context->files)->toBe('file1.php')
            ->and($context->composer)->toBe('{"name": "test"}')
            ->and($context->package)->toBe('{"name": "frontend"}');
    });

    it('allows null composer and package', function () {
        $context = new ReadmeContext(
            files: 'file.php',
            composer: null,
            package: null
        );

        expect($context->composer)->toBeNull()
            ->and($context->package)->toBeNull();
    });
});
