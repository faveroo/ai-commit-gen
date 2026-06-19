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
    public function install()
    {
        if(is_dir($this->ollamaPath)) {
            return "Ollama is already installed. Continuing to model instalation";
            
        }

        $process = Process::fromShellCommandline(
            'powershell -ExecutionPolicy Bypass -Command "irm https://ollama.com/install.ps1 | iex"'
        );

        $process->run();
        
        if($process->isSuccessful()) {
            return 'Ollama was installed successfully';
        }

        return 'An error was ocurred: ' . $process->getErrorOutput();
    }

    public function installModel(string $model)
    {
        $process = Process::fromShellCommandline(
            sprintf('ollama pull %s', $model)
        );

        $process->start();

        foreach ($process as $type => $data) {
            print($data);
        }

        return 'Model installed!';
    }

    public function ensureIfNotInstalled($model)
    {
        $list = Process::fromShellCommandline('ollama list');
        $list->run();

        if(str_contains($list->getOutput(), $model)) {
            return false;
        }

        return true;
    }
}