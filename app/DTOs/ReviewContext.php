<?php

namespace App\DTOs;

class ReviewContext extends BaseContext
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
}