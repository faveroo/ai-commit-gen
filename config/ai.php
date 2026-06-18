<?php

return [
    'provider' => env('AI_PROVIDER', 'gemini'),

    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL')
    ]
];