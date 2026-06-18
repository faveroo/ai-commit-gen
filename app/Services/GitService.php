<?php

namespace App\Services;

use Symfony\Component\Process\Process;

class GitService
{
    public function stagedDiff(): string
    {
        $process = Process::fromShellCommandline(
            'git diff --cached'
        );

        $process->run();

        return $process->getOutput();
    }

    public function status(): string
    {
        $process = Process::fromShellCommandline(
            'git status --short'
        );

        $process->run();

        return $process->getOutput();
    }

    public function commit(string $message): void
    {
        $process = Process::fromShellCommandline(
            sprintf(
                'git commit -m "%s"',
                addslashes($message)    
            )
        );

        $process->run();
    } 
}

