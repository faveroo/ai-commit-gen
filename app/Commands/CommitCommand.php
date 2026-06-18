<?php

namespace App\Commands;

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
        $diff = $git->stagedDiff();
        $status = $git->status();

        if(blank($diff)) {
            $this->error(
                "No staged changes found"
            );

            return self::FAILURE;
        }

        if(blank($status)) {
            $this->error(
                "No status found"
            );
        }

        $message = "";

        $this->task('Generating commit message', 
        function() use (
            &$message,
            $aiService,
            $diff,
            $status,
        ) {
            $message = $aiService->generateCommit($diff, $status);

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
