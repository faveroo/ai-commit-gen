<?php

namespace App\Commands;

use App\Services\OllamaConfigService;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class InstallOllamaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ollama:model {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install a ollama model.';

    /**
     * Execute the console command.
     */
    public function handle(
        OllamaConfigService $service
    ) {
        $model = $this->argument('model');

        if (! $service->installation()) {
            $this->warn("Ollama is not installed. Please install in 'ollama.com/download'");
            return self::FAILURE;
        }

        if (! $service->ensureIfNotInstalled($model)) {
            $this->warn('Model already installed.');

            return self::SUCCESS;
        }

        if (! $service->installModel($model)) {
            $this->error('Error during installation');
            return self::FAILURE;
        }

        $this->warn('The model was installed successfully.');

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
