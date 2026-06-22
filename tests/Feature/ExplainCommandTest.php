<?php

use App\Services\AiService;
use App\Services\GitService;

it('fails when there are no staged changes', function () {
    $gitMock = Mockery::mock(GitService::class);
    $gitMock->shouldReceive('branch')->andReturn('main');
    $gitMock->shouldReceive('status')->andReturn('');
    $gitMock->shouldReceive('stagedDiff')->andReturn('');

    $this->app->instance(GitService::class, $gitMock);

    $this->artisan('explain')
        ->expectsOutputToContain('No staged changes found')
        ->assertExitCode(1);
});

it('generates and displays an explanation', function () {
    $gitMock = Mockery::mock(GitService::class);
    $gitMock->shouldReceive('branch')->andReturn('feature/refactor');
    $gitMock->shouldReceive('status')->andReturn('M app/Service.php');
    $gitMock->shouldReceive('stagedDiff')->andReturn('+refactored code');

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
