<?php

namespace App\DTOs;

abstract class BaseContext
{
    

    protected static function sanitizeJson(string $content): string
    {
        $content = trim($content);

        $content = preg_replace('/^```json\s*/', '', $content);
        $content = preg_replace('/^``json\s*/', '', $content);
        $content = preg_replace('/\s*```$/', '', $content);
        $content = preg_replace('/\s*``$/', '', $content);

        return trim($content);
    }
}