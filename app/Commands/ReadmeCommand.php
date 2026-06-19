<?php

namespace App\Commands;

use App\DTOs\ReadmeContext;
use App\Prompts\ReadmePrompt;
use App\Repositories\ProjectRepository;
use App\Services\AiService;
use App\Services\GitService;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class ReadmeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:readme';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle
    (
        AiService $ai,
        GitService $git,
        ProjectRepository $projectRepository
    )
    {
        $files = $git->listFiles();
        $pc = $projectRepository->getProjectContext();
        $context = new ReadmeContext($files, $pc['composer'], $pc['package']);
        $prompt = ReadmePrompt::build($context);
        $markdown = $ai->generate($prompt);

        if (
            file_exists('README.md')
            && ! $this->confirm(
                'README.md already exists. Overwrite?'
            )
        ) {
            return self::FAILURE;
        }

        file_put_contents(
            'README.md',
            $markdown
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
