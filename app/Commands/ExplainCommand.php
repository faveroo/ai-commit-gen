<?php

namespace App\Commands;

use App\Services\AiService;
use App\Services\GitService;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class ExplainCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explain';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate an explanation about git diff --cached';

    /**
     * Execute the console command.
     */
    public function handle
    (
        GitService $gitService,
        AiService $aiService
    )
    {
        $diff = $gitService->stagedDiff();

        if(blank($diff)) {
            $this->error("No staged changes found");

            return self::FAILURE;
        }

        $message = "";

        $this->task(
            "Generating Explanation",
            function()
            use (
                $diff,
                &$message,
                $aiService,
            ) {
                $message = $aiService->generateExplanation($diff);

                return true;
            }
        );

        $this->newLine();

        $this->line("<fg=green>{$message}</>");

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
