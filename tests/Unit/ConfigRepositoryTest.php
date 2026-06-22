<?php

use App\Repositories\ConfigRepository;

beforeEach(function () {
    $this->tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'git-ai-test-' . uniqid();
    $this->configPath = $this->tempDir . DIRECTORY_SEPARATOR . '.git-ai' . DIRECTORY_SEPARATOR . 'config.json';

    // Override the HOME/USERPROFILE so ConfigRepository uses our temp dir
    $_SERVER['HOME'] = $this->tempDir;
    $_SERVER['USERPROFILE'] = $this->tempDir;
});

afterEach(function () {
    // Clean up temp files
    if (file_exists($this->configPath)) {
        unlink($this->configPath);
    }

    $dir = dirname($this->configPath);
    if (is_dir($dir)) {
        rmdir($dir);
    }

    if (is_dir($this->tempDir)) {
        rmdir($this->tempDir);
    }

    unset($_SERVER['HOME'], $_SERVER['USERPROFILE']);
});

it('creates the config directory and file on construction', function () {
    $repo = new ConfigRepository();

    expect(file_exists($repo->configPath))->toBeTrue()
        ->and(is_dir(dirname($repo->configPath)))->toBeTrue();
});

it('creates an empty json object as default config', function () {
    $repo = new ConfigRepository();

    $content = file_get_contents($repo->configPath);

    expect($content)->toBe('{}');
});

it('returns null for a non-existent key', function () {
    $repo = new ConfigRepository();

    expect($repo->get('nonexistent'))->toBeNull();
});

it('sets and gets a value', function () {
    $repo = new ConfigRepository();

    $repo->set('provider', 'gemini');

    expect($repo->get('provider'))->toBe('gemini');
});

it('overwrites an existing value', function () {
    $repo = new ConfigRepository();

    $repo->set('provider', 'gemini');
    $repo->set('provider', 'ollama');

    expect($repo->get('provider'))->toBe('ollama');
});

it('persists values to disk', function () {
    $repo = new ConfigRepository();
    $repo->set('model', 'gemini-2.5-flash');

    // Create a new instance to verify disk persistence
    $repo2 = new ConfigRepository();

    expect($repo2->get('model'))->toBe('gemini-2.5-flash');
});

it('returns all config values', function () {
    $repo = new ConfigRepository();
    $repo->set('provider', 'gemini');
    $repo->set('model', 'gemini-2.5-flash');

    $all = $repo->all();

    expect($all)->toBe([
        'provider' => 'gemini',
        'model' => 'gemini-2.5-flash',
    ]);
});

it('returns an empty array when config is empty', function () {
    $repo = new ConfigRepository();

    expect($repo->all())->toBe([]);
});

it('stores config as pretty-printed json', function () {
    $repo = new ConfigRepository();
    $repo->set('provider', 'ollama');

    $raw = file_get_contents($repo->configPath);
    $decoded = json_decode($raw, true);

    expect($decoded)->toBe(['provider' => 'ollama'])
        ->and($raw)->toContain("\n"); // pretty-printed has newlines
});

it('handles multiple keys independently', function () {
    $repo = new ConfigRepository();

    $repo->set('provider', 'gemini');
    $repo->set('model', 'gemini-2.5-pro');
    $repo->set('custom-key', 'custom-value');

    expect($repo->get('provider'))->toBe('gemini')
        ->and($repo->get('model'))->toBe('gemini-2.5-pro')
        ->and($repo->get('custom-key'))->toBe('custom-value');
});
