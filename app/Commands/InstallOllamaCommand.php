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
    protected $signature = 'ollama:install {--model= : Model name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install ollama and specific model. Use --model= to specify.';

    /**
     * Execute the console command.
     */
    public function handle
    (
        OllamaConfigService $service
    )
    {
        $model = $this->option('model');

        $this->info($service->install());

        if(!$service->ensureIfNotInstalled($model)) {
            $this->warn('Model already installed.');

            return self::SUCCESS;
        }

        $this->info($service->installModel($model));

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
