<?php

namespace App\Repositories;

class ConfigRepository
{
    public string $configPath;

    public function __construct()
    {
        $home = $_SERVER['HOME']
            ?? $_SERVER['USERPROFILE']
            ?? getcwd();

        $this->configPath = $home . DIRECTORY_SEPARATOR . '.git-ai' . DIRECTORY_SEPARATOR . 'config.json';

        $this->ensureConfigExists();
    }
    public function get(string $key)
    {
        $config = $this->all();

        return $config[$key] ?? null;
    }

    public function set(
        string $key,
        mixed $value
    ): void {
        $config = $this->all();

        $config[$key] = $value;

        file_put_contents(
            $this->configPath,
            json_encode(
                $config,
                JSON_PRETTY_PRINT
            )
        );
    }

    public function all(): array
    {
        return json_decode(
            file_get_contents($this->configPath),
            true
        ) ?? [];
    }

    public function ensureConfigExists(): void
    {
        $directory = dirname($this->configPath);

        if(!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if(!file_exists($this->configPath)) {
            file_put_contents($this->configPath, '{}');
        }
    }
}
