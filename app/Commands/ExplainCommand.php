<?php

namespace App\Commands;

use App\DTOs\CommitContext;
use App\Prompts\ExplainPrompt;
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
    public function handle(
        GitService $gitService,
        AiService $aiService
    ) {
        $context = $gitService->buildStagedContext();

        if (blank($context->diff)) {
            $this->error("No staged changes found");

            return self::FAILURE;
        }

        $message = "";

        $this->task(
            "Generating Explanation",
            function ()
            use (
                $context,
                &$message,
                $aiService,
            ) {
                $prompt = ExplainPrompt::build($context);
                $message = $aiService->generate($prompt);

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
