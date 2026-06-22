<?php

use App\Repositories\ConfigRepository;

it('displays all config values in a table', function () {
    $configMock = Mockery::mock(ConfigRepository::class);
    $configMock->shouldReceive('all')
        ->once()
        ->andReturn([
            'provider' => 'gemini',
            'model' => 'gemini-2.5-flash',
        ]);

    $this->app->instance(ConfigRepository::class, $configMock);

    $this->artisan('config:list')
        ->expectsTable(
            ['Key', 'Value'],
            [
                ['provider', 'gemini'],
                ['model', 'gemini-2.5-flash'],
            ]
        )
        ->assertExitCode(0);
});

it('displays empty table when no config is set', function () {
    $configMock = Mockery::mock(ConfigRepository::class);
    $configMock->shouldReceive('all')
        ->once()
        ->andReturn([]);

    $this->app->instance(ConfigRepository::class, $configMock);

    $this->artisan('config:list')
        ->assertExitCode(0);
});
