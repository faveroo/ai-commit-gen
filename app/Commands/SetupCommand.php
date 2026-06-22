<?php

namespace App\Commands;

use App\Repositories\ConfigRepository;
use App\Repositories\ProjectRepository;
use App\Services\OllamaConfigService;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class SetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(
        ConfigRepository $configRepository,
        OllamaConfigService $ollamaConfigService
    ) {
        $this->info("Running Setup!");

        $option = $this->choice(
            'Which AI provider will we use?',
            [
                'Gemini Provider',
                'Ollama Provider'
            ],
            0
        );

        if ($option === "Gemini Provider") {
            $configRepository->set("provider", "gemini");

            $key = $this->ask("Gemini API Key: ");
            $configRepository->set('api-key', $key);

            $model = $this->ask("Gemini model: ");
            $configRepository->set('model', $model);

            $this->info("Gemini Provider configured!");
        } else if ($option === "Ollama Provider") {
            $configRepository->set("provider", "ollama");

            if (! $ollamaConfigService->installation()) {
                $this->error("Please install ollama before continuing. 'ollama.com/download'");
                return self::FAILURE;
            }

            $model = $this->ask("Which Ollama model we should install? ex: llama3.2:3b");

            $ollamaConfigService->installModel($model);

            $this->info("Ollama Provider configured!");
        }

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
