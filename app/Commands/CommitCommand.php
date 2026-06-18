<?php

namespace App\Commands;

use App\DTOs\CommitContext;
use App\Services\AiService;
use App\Services\GitService;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class CommitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description =
        'Generate commit message with Gemini';

    /**
     * Execute the console command.
     */
    public function handle
    (
        GitService $git,
        AiService $aiService
    )
    {
        $context = new CommitContext($git->stagedDiff(), $git->status(), $git->branch());

        if(blank($context->diff)) {
            $this->error(
                "No staged changes found"
            );

            return self::FAILURE;
        }

        if(blank($context->files)) {
            $this->error(
                "No status found"
            );
        }

        $message = "";

        $this->task('Generating commit message', 
        function() use (
            &$message,
            $aiService,
            $context,
        ) {
            $message = $aiService->generateCommit($context);
            
            if(blank($message)) {
                print('No content in $message');
                return self::FAILURE;
            }

            return true;
        });

        $this->newLine();

        $this->line("<fg=green>{$message}</>");

        $this->newLine();

        if(!$this->confirm("Commit changes?")) {
            return self::SUCCESS;
        }

        $git->commit($message);

        $this->info('Commit created.');

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
