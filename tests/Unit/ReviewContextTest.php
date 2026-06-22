<?php

use App\DTOs\ReviewContext;

it('parses valid json into a ReviewContext', function () {
    $json = json_encode([
        'summary' => 'Added new feature',
        'risk' => 'LOW',
        'issues' => [
            [
                'severity' => 'low',
                'category' => 'Maintainability',
                'title' => 'Missing docblock',
            ],
        ],
        'recommendations' => [
            [
                'priority' => 'low',
                'title' => 'Add docblock',
                'description' => 'Add a docblock to the method',
            ],
        ],
    ]);

    $review = ReviewContext::fromJson($json);

    expect($review->summary)->toBe('Added new feature')
        ->and($review->risk)->toBe('LOW')
        ->and($review->issues)->toHaveCount(1)
        ->and($review->issues[0]['severity'])->toBe('low')
        ->and($review->issues[0]['category'])->toBe('Maintainability')
        ->and($review->issues[0]['title'])->toBe('Missing docblock')
        ->and($review->recommendations)->toHaveCount(1);
});

it('handles missing optional fields with defaults', function () {
    $json = json_encode([]);

    $review = ReviewContext::fromJson($json);

    expect($review->summary)->toBe('')
        ->and($review->risk)->toBe('')
        ->and($review->issues)->toBe([])
        ->and($review->recommendations)->toBe([]);
});

it('strips markdown json code fences', function () {
    $wrapped = "```json\n" . json_encode([
        'summary' => 'Clean code',
        'risk' => 'NONE',
        'issues' => [],
        'recommendations' => [],
    ]) . "\n```";

    $review = ReviewContext::fromJson($wrapped);

    expect($review->summary)->toBe('Clean code')
        ->and($review->risk)->toBe('NONE');
});

it('strips double backtick json fences', function () {
    $wrapped = "``json\n" . json_encode([
        'summary' => 'Test',
        'risk' => 'LOW',
        'issues' => [],
        'recommendations' => [],
    ]) . "\n``";

    $review = ReviewContext::fromJson($wrapped);

    expect($review->summary)->toBe('Test');
});

it('throws on completely invalid json', function () {
    ReviewContext::fromJson('this is not json at all');
})->throws(JsonException::class);

it('throws on empty string', function () {
    ReviewContext::fromJson('');
})->throws(JsonException::class);

it('handles json with extra whitespace', function () {
    $json = "   \n\n  " . json_encode([
        'summary' => 'Whitespace test',
        'risk' => 'LOW',
        'issues' => [],
        'recommendations' => [],
    ]) . "  \n  ";

    $review = ReviewContext::fromJson($json);

    expect($review->summary)->toBe('Whitespace test');
});

it('converts to array correctly', function () {
    $review = new ReviewContext(
        summary: 'Test summary',
        risk: 'HIGH',
        issues: [['severity' => 'high', 'title' => 'Bug']],
        recommendations: ['Fix the bug']
    );

    $array = $review->toArray();

    expect($array)->toBe([
        'summary' => 'Test summary',
        'risk' => 'HIGH',
        'issues' => [['severity' => 'high', 'title' => 'Bug']],
        'recommendations' => ['Fix the bug'],
    ]);
});

it('handles multiple issues and recommendations', function () {
    $json = json_encode([
        'summary' => 'Complex review',
        'risk' => 'HIGH',
        'issues' => [
            ['severity' => 'high', 'category' => 'Security', 'title' => 'SQL injection'],
            ['severity' => 'medium', 'category' => 'Performance', 'title' => 'N+1 query'],
            ['severity' => 'low', 'category' => 'Maintainability', 'title' => 'Magic number'],
        ],
        'recommendations' => [
            ['priority' => 'high', 'title' => 'Parameterize queries', 'description' => 'Use bindings'],
            ['priority' => 'medium', 'title' => 'Eager load', 'description' => 'Use with()'],
        ],
    ]);

    $review = ReviewContext::fromJson($json);

    expect($review->issues)->toHaveCount(3)
        ->and($review->recommendations)->toHaveCount(2)
        ->and($review->issues[0]['title'])->toBe('SQL injection')
        ->and($review->issues[2]['title'])->toBe('Magic number');
});
