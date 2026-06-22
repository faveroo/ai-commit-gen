<?php

namespace App\DTOs;

class ReviewContext
{
    public function __construct
    (
        public string $summary,
        public string $risk,
        public array $issues,
        public array $recommendations
    ) {}

    public static function fromJson(string $content): self
    {
        $content = self::sanitizeJson($content);

        $data = json_decode(
            $content,
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        return new self(
            summary: $data['summary'] ?? '',
            risk: $data['risk'] ?? '',
            issues: $data['issues'] ?? [],
            recommendations: $data['recommendations'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'summary' => $this->summary,
            'risk' => $this->risk,
            'issues' => $this->issues,
            'recommendations' => $this->recommendations
        ];
    }

    private static function sanitizeJson(string $content): string
    {
        $content = trim($content);

        $content = preg_replace('/^```json\s*/', '', $content);
        $content = preg_replace('/^``json\s*/', '', $content);
        $content = preg_replace('/\s*```$/', '', $content);
        $content = preg_replace('/\s*``$/', '', $content);

        return trim($content);
    }
}