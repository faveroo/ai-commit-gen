<?php

use App\AI\Contracts\AIProvider;
use App\Services\AiService;

it('delegates generate call to the provider', function () {
    $mockProvider = Mockery::mock(AIProvider::class);
    $mockProvider->shouldReceive('generate')
        ->once()
        ->with('test prompt')
        ->andReturn('generated response');

    $service = new AiService($mockProvider);

    $result = $service->generate('test prompt');

    expect($result)->toBe('generated response');
});

it('returns the provider response as-is', function () {
    $mockProvider = Mockery::mock(AIProvider::class);
    $mockProvider->shouldReceive('generate')
        ->andReturn('feat(auth): add login support');

    $service = new AiService($mockProvider);

    expect($service->generate('any prompt'))->toBe('feat(auth): add login support');
});

it('propagates exceptions from the provider', function () {
    $mockProvider = Mockery::mock(AIProvider::class);
    $mockProvider->shouldReceive('generate')
        ->andThrow(new RuntimeException('API error'));

    $service = new AiService($mockProvider);

    $service->generate('test');
})->throws(RuntimeException::class, 'API error');
