<?php

namespace App\Services;

use Symfony\Component\Process\Process;


class OllamaConfigService
{
    public string $ollamaPath;
    public function __construct()
    {
        $home = $_SERVER['HOME']
            ?? $_SERVER['USERPROFILE']
            ?? getcwd();

        $this->ollamaPath = $home .  DIRECTORY_SEPARATOR . '.ollama' . DIRECTORY_SEPARATOR;
    }
    public function installation()
    {
        if (!is_dir($this->ollamaPath)) {
            return false;
        }

        return true;
    }

    public function installModel(string $model)
    {
        $process = Process::fromShellCommandline(
            sprintf('ollama pull %s', $model),
            timeout: 360
        );

        $process->start();

        foreach ($process as $type => $data) {
            print($data);
        }

        if (! $process->isSuccessful()) {
            return false;
        }

        return true;
    }

    public function ensureIfNotInstalled(string $model)
    {
        $list = Process::fromShellCommandline('ollama list');
        $list->run();

        if (str_contains($list->getOutput(), $model)) {
            return false;
        }

        return true;
    }
}
