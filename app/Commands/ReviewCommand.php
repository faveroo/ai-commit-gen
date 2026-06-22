<?php

namespace App\Commands;

use App\DTOs\CommitContext;
use App\DTOs\ReviewContext;
use App\Prompts\ReviewPrompt;
use App\Services\AiService;
use App\Services\GitService;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class ReviewCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'review';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reviews the entire git diff --cached';

    /**
     * Execute the console command.
     */
    public function handle
    (
        GitService $gitService,
        AiService $aiService
    )
    {
        $context = new CommitContext(
            branch: $gitService->branch(),
            files: $gitService->status(),
            diff: $gitService->stagedDiff()
        );

        $prompt = ReviewPrompt::build($context);
        $response = $aiService->generate($prompt);

        $review = ReviewContext::fromJson($response);

        $rows = collect($review->issues)
            ->map(fn ($issue) => [
                $issue['severity'] ?? '',
                $issue['category'] ?? '',
                $issue['title'] ?? ''
            ])
            ->toArray();
        
        $this->table(
            ['Summary', 'Risks'],
            [
                [$review->summary, $review->risk],
            ]
        );

        $this->table(
            ['Severity', 'Category', 'Issue'],
            $rows
        );

        return self::SUCCESS;
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
