<?php

use App\Contracts\AIProvider;
use App\Factories\AIProviderFactory;
use App\Services\AiService;

it('delegates generate call to the provider', function () {
    $mockProvider = Mockery::mock(AIProvider::class);
    $mockProvider->shouldReceive('generate')
        ->once()
        ->with('test prompt')
        ->andReturn('generated response');

    $mockFactory = Mockery::mock(AIProviderFactory::class);
    $mockFactory->shouldReceive('make')
        ->once()
        ->andReturn($mockProvider);

    $service = new AiService($mockFactory);

    $result = $service->generate('test prompt');

    expect($result)->toBe('generated response');
});

it('returns the provider response as-is', function () {
    $mockProvider = Mockery::mock(AIProvider::class);
    $mockProvider->shouldReceive('generate')
        ->andReturn('feat(auth): add login support');

    $mockFactory = Mockery::mock(AIProviderFactory::class);
    $mockFactory->shouldReceive('make')
        ->andReturn($mockProvider);

    $service = new AiService($mockFactory);

    expect($service->generate('any prompt'))->toBe('feat(auth): add login support');
});

it('propagates exceptions from the factory', function () {
    $mockFactory = Mockery::mock(AIProviderFactory::class);
    $mockFactory->shouldReceive('make')
        ->andThrow(new RuntimeException('No provider configured'));

    $service = new AiService($mockFactory);

    $service->generate('test');
})->throws(RuntimeException::class, 'No provider configured');

it('propagates exceptions from the provider', function () {
    $mockProvider = Mockery::mock(AIProvider::class);
    $mockProvider->shouldReceive('generate')
        ->andThrow(new RuntimeException('API error'));

    $mockFactory = Mockery::mock(AIProviderFactory::class);
    $mockFactory->shouldReceive('make')
        ->andReturn($mockProvider);

    $service = new AiService($mockFactory);

    $service->generate('test');
})->throws(RuntimeException::class, 'API error');
