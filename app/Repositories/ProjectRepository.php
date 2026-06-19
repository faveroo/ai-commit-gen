<?php

namespace App\Repositories;

class ProjectRepository
{
    public function getProjectContext(): array
    {
        return [
            'composer' => file_exists('composer.json') 
                ? file_get_contents('composer.json')
                : null,
            'package' => file_exists('package.json')
                ? file_get_contents('package.json')
                : null,
        ];
    }
}