<?php

use App\Repositories\ConfigRepository;
use App\Services\OllamaConfigService;

it('fails with an invalid key', function () {
    $this->artisan('config:set', ['key' => 'invalid-key'])
        ->expectsOutputToContain('You should send an valid key name')
        ->assertExitCode(1);
});

it('sets the provider via choice', function () {
    $configMock = Mockery::mock(ConfigRepository::class);
    $configMock->shouldReceive('set')
        ->once()
        ->with('provider', 'gemini');

    $ollamaMock = Mockery::mock(OllamaConfigService::class);
    $this->app->instance(OllamaConfigService::class, $ollamaMock);

    $this->app->instance(ConfigRepository::class, $configMock);

    $this->artisan('config:set', ['key' => 'provider'])
        ->expectsChoice('Choose your provider', 'gemini', ['gemini', 'ollama'])
        ->expectsOutputToContain('The provider was configured')
        ->assertExitCode(0);
});

it('sets the model for gemini provider', function () {
    $configMock = Mockery::mock(ConfigRepository::class);
    $configMock->shouldReceive('get')
        ->with('provider')
        ->andReturn('gemini');
    $configMock->shouldReceive('set')
        ->once()
        ->with('model', 'gemini-2.5-flash');

    $ollamaMock = Mockery::mock(OllamaConfigService::class);
    $ollamaMock->shouldReceive('ensureIfNotInstalled')->with('gemini-2.5-flash')->andReturn(false);

    $this->app->instance(OllamaConfigService::class, $ollamaMock);

    $this->app->instance(ConfigRepository::class, $configMock);

    $this->artisan('config:set', ['key' => 'model'])
        ->expectsChoice('Choose your model', 'gemini-2.5-flash', ['gemini-2.5-flash', 'gemini-2.0-flash', 'gemini-2.5-pro'])
        ->expectsOutputToContain('The model was configured')
        ->assertExitCode(0);
});

it('sets the model for ollama provider', function () {
    $configMock = Mockery::mock(ConfigRepository::class);
    $configMock->shouldReceive('get')
        ->with('provider')
        ->andReturn('ollama');
    $configMock->shouldReceive('set')
        ->once()
        ->with('model', 'llama3.2:3b');

    $ollamaMock = Mockery::mock(OllamaConfigService::class);
    $ollamaMock->shouldReceive('ensureIfNotInstalled')->with('llama3.2:3b')->andReturn(false);

    $this->app->instance(OllamaConfigService::class, $ollamaMock);

    $this->app->instance(ConfigRepository::class, $configMock);

    $this->artisan('config:set', ['key' => 'model'])
        ->expectsChoice('Choose your model', 'llama3.2:3b', ['llama3.2:1b', 'llama3.2:3b', 'qwen3:8b'])
        ->expectsOutputToContain('The model was configured')
        ->assertExitCode(0);
});
