<?php

use App\Services\AiService;
use App\Services\GitService;

use App\DTOs\DiffContext;

it('fails when there are no staged changes', function () {
    $gitMock = Mockery::mock(GitService::class);
    $gitMock->shouldReceive('buildStagedContext')->andReturn(new DiffContext('main', '', ''));

    $this->app->instance(GitService::class, $gitMock);

    $this->artisan('explain')
        ->expectsOutputToContain('No staged changes found')
        ->assertExitCode(1);
});

it('generates and displays an explanation', function () {
    $gitMock = Mockery::mock(GitService::class);
    $gitMock->shouldReceive('buildStagedContext')->andReturn(new DiffContext('feature/refactor', 'M app/Service.php', '+refactored code'));

    $aiMock = Mockery::mock(AiService::class);
    $aiMock->shouldReceive('generate')
        ->once()
        ->andReturn('This change refactors the service layer.');

    $this->app->instance(GitService::class, $gitMock);
    $this->app->instance(AiService::class, $aiMock);

    $this->artisan('explain')
        ->expectsOutputToContain('This change refactors the service layer.')
        ->assertExitCode(0);
});
