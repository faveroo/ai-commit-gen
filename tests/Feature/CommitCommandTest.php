<?php

use App\Services\AiService;
use App\Services\GitService;

it('fails when there are no staged changes', function () {
    $gitMock = Mockery::mock(GitService::class);
    $gitMock->shouldReceive('branch')->andReturn('main');
    $gitMock->shouldReceive('status')->andReturn('');
    $gitMock->shouldReceive('stagedDiff')->andReturn('');

    $this->app->instance(GitService::class, $gitMock);

    $this->artisan('commit')
        ->expectsOutputToContain('No staged changes found')
        ->assertExitCode(1);
});

it('fails when the diff is too large', function () {
    $largeDiff = str_repeat('+ added line content here for padding', 5000);

    $gitMock = Mockery::mock(GitService::class);
    $gitMock->shouldReceive('branch')->andReturn('main');
    $gitMock->shouldReceive('status')->andReturn('M large-file.php');
    $gitMock->shouldReceive('stagedDiff')->andReturn($largeDiff);

    $this->app->instance(GitService::class, $gitMock);

    $this->artisan('commit')
        ->expectsOutputToContain('Diff too large')
        ->assertExitCode(1);
});

it('generates a commit message and allows declining', function () {
    $gitMock = Mockery::mock(GitService::class);
    $gitMock->shouldReceive('branch')->andReturn('feature/login');
    $gitMock->shouldReceive('status')->andReturn('M app/Auth.php');
    $gitMock->shouldReceive('stagedDiff')->andReturn('+public function login()');

    $aiMock = Mockery::mock(AiService::class);
    $aiMock->shouldReceive('generate')
        ->once()
        ->andReturn('feat(auth): add login method');

    $this->app->instance(GitService::class, $gitMock);
    $this->app->instance(AiService::class, $aiMock);

    $this->artisan('commit')
        ->expectsOutputToContain('feat(auth): add login method')
        ->expectsConfirmation('Commit changes?', 'no')
        ->assertExitCode(0);
});

it('commits when user confirms', function () {
    $gitMock = Mockery::mock(GitService::class);
    $gitMock->shouldReceive('branch')->andReturn('main');
    $gitMock->shouldReceive('status')->andReturn('M app/Service.php');
    $gitMock->shouldReceive('stagedDiff')->andReturn('+new code');
    $gitMock->shouldReceive('commit')
        ->once()
        ->with('fix(service): resolve null check');

    $aiMock = Mockery::mock(AiService::class);
    $aiMock->shouldReceive('generate')
        ->once()
        ->andReturn('fix(service): resolve null check');

    $this->app->instance(GitService::class, $gitMock);
    $this->app->instance(AiService::class, $aiMock);

    $this->artisan('commit')
        ->expectsConfirmation('Commit changes?', 'yes')
        ->expectsOutputToContain('Commit created')
        ->assertExitCode(0);
});

it('handles runtime exceptions from the ai service', function () {
    $gitMock = Mockery::mock(GitService::class);
    $gitMock->shouldReceive('branch')->andReturn('main');
    $gitMock->shouldReceive('status')->andReturn('M file.php');
    $gitMock->shouldReceive('stagedDiff')->andReturn('+code');

    $aiMock = Mockery::mock(AiService::class);
    $aiMock->shouldReceive('generate')
        ->andThrow(new RuntimeException('No provider configured'));

    $this->app->instance(GitService::class, $gitMock);
    $this->app->instance(AiService::class, $aiMock);

    $this->artisan('commit')
        ->expectsOutputToContain('No provider configured')
        ->assertExitCode(1);
});
