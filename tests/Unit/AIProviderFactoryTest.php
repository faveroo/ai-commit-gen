<?php

use App\Factories\AIProviderFactory;
use App\Providers\GeminiProvider;
use App\Providers\OllamaProvider;
use App\Repositories\ConfigRepository;

beforeEach(function () {
    $this->config = Mockery::mock(ConfigRepository::class);
    putenv('GEMINI_API_KEY=');
    unset($_ENV['GEMINI_API_KEY'], $_SERVER['GEMINI_API_KEY']);
});

it('throws when no provider is configured', function () {
    $this->config->shouldReceive('get')
        ->with('provider')
        ->andReturn(null);

    $factory = new AIProviderFactory($this->config);

    $factory->make();
})->throws(RuntimeException::class, 'No provider configured');

it('throws when an unknown provider is configured', function () {
    $this->config->shouldReceive('get')
        ->with('provider')
        ->andReturn('openai');

    $factory = new AIProviderFactory($this->config);

    $factory->make();
})->throws(RuntimeException::class, 'Unknown provider [openai]');

it('creates a GeminiProvider when provider is gemini', function () {
    $this->config->shouldReceive('get')
        ->with('provider')
        ->andReturn('gemini');

    $this->config->shouldReceive('get')
        ->with('model')
        ->andReturn('gemini-2.5-flash');

    putenv('GEMINI_API_KEY=test-api-key-123');

    $factory = new AIProviderFactory($this->config);
    $provider = $factory->make();

    expect($provider)->toBeInstanceOf(GeminiProvider::class);

    putenv('GEMINI_API_KEY');
});

it('creates an OllamaProvider when provider is ollama', function () {
    $this->config->shouldReceive('get')
        ->with('provider')
        ->andReturn('ollama');

    $this->config->shouldReceive('get')
        ->with('model')
        ->andReturn('llama3.2:3b');

    $factory = new AIProviderFactory($this->config);
    $provider = $factory->make();

    expect($provider)->toBeInstanceOf(OllamaProvider::class);
});

it('throws when gemini api key is missing', function () {
    $this->config->shouldReceive('get')
        ->with('provider')
        ->andReturn('gemini');

    putenv('GEMINI_API_KEY');

    $factory = new AIProviderFactory($this->config);

    $factory->makeGemini();
})->throws(RuntimeException::class, 'Gemini API key not configured');

it('throws when gemini model is missing', function () {
    $this->config->shouldReceive('get')
        ->with('provider')
        ->andReturn('gemini');

    $this->config->shouldReceive('get')
        ->with('model')
        ->andReturn(null);

    putenv('GEMINI_API_KEY=test-api-key-123');

    $factory = new AIProviderFactory($this->config);

    try {
        $factory->makeGemini();
    } finally {
        putenv('GEMINI_API_KEY');
    }
})->throws(RuntimeException::class, 'Gemini API model not configured');

it('throws when ollama model is missing', function () {
    $this->config->shouldReceive('get')
        ->with('provider')
        ->andReturn('ollama');

    $this->config->shouldReceive('get')
        ->with('model')
        ->andReturn(null);

    $factory = new AIProviderFactory($this->config);

    $factory->makeOllama();
})->throws(RuntimeException::class, 'Ollama model not configured');
