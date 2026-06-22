<?php

use App\Services\AiService;
use App\Services\GitService;

use App\DTOs\DiffContext;

it('displays review results in tables', function () {
    $reviewJson = json_encode([
        'summary' => 'Added authentication module',
        'risk' => 'MEDIUM',
        'issues' => [
            [
                'severity' => 'high',
                'category' => 'Security',
                'title' => 'Missing input validation',
            ],
        ],
        'recommendations' => [
            [
                'priority' => 'high',
                'title' => 'Add validation',
                'description' => 'Validate all user inputs',
            ],
        ],
    ]);

    $gitMock = Mockery::mock(GitService::class);
    $gitMock->shouldReceive('buildStagedContext')->andReturn(new DiffContext('main', 'M app/Auth.php', '+new code'));

    $aiMock = Mockery::mock(AiService::class);
    $aiMock->shouldReceive('generate')
        ->once()
        ->andReturn($reviewJson);

    $this->app->instance(GitService::class, $gitMock);
    $this->app->instance(AiService::class, $aiMock);

    $this->artisan('review')
        ->expectsTable(
            ['Summary', 'Risks'],
            [['Added authentication module', 'MEDIUM']]
        )
        ->expectsTable(
            ['Severity', 'Category', 'Issue'],
            [['high', 'Security', 'Missing input validation']]
        )
        ->assertExitCode(0);
});

it('handles review with no issues', function () {
    $reviewJson = json_encode([
        'summary' => 'Clean code change',
        'risk' => 'LOW',
        'issues' => [],
        'recommendations' => [],
    ]);

    $gitMock = Mockery::mock(GitService::class);
    $gitMock->shouldReceive('buildStagedContext')->andReturn(new DiffContext('main', 'M app/Service.php', '+clean code'));

    $aiMock = Mockery::mock(AiService::class);
    $aiMock->shouldReceive('generate')->andReturn($reviewJson);

    $this->app->instance(GitService::class, $gitMock);
    $this->app->instance(AiService::class, $aiMock);

    $this->artisan('review')
        ->assertExitCode(0);
});

it('handles json wrapped in code fences', function () {
    $wrapped = "```json\n" . json_encode([
        'summary' => 'Wrapped review',
        'risk' => 'LOW',
        'issues' => [],
        'recommendations' => [],
    ]) . "\n```";

    $gitMock = Mockery::mock(GitService::class);
    $gitMock->shouldReceive('buildStagedContext')->andReturn(new DiffContext('main', 'M file.php', '+code'));

    $aiMock = Mockery::mock(AiService::class);
    $aiMock->shouldReceive('generate')->andReturn($wrapped);

    $this->app->instance(GitService::class, $gitMock);
    $this->app->instance(AiService::class, $aiMock);

    $this->artisan('review')
        ->assertExitCode(0);
});
