<?php

namespace App\Commands;

use App\Repositories\ConfigRepository;
use App\Services\OllamaConfigService;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class ConfigSetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'config:set {key}';

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
        OllamaConfigService $service
    ) {
        if ($this->argument('key') === 'provider') {
            $provider = $this->choice('Choose your provider', ['gemini', 'ollama']);
            $configRepository->set('provider', $provider);

            $this->info("The provider was configured!");
        } else if ($this->argument('key') === 'model') {
            $provider = $configRepository->get('provider');
            $model = match ($provider) {
                'gemini' => $this->choice('Choose your model', ['gemini-2.5-flash', 'gemini-2.0-flash', 'gemini-2.5-pro']),
                'ollama' => $this->choice('Choose your model', ['llama3.2:1b', 'llama3.2:3b', 'qwen3:8b'])
            };

            if ($service->ensureIfNotInstalled($model)) {
                $service->installModel($model);
            }

            $configRepository->set('model', $model);

            $this->info("The model was configured!");
        } else {

            $this->error('You should send an valid key name: provider, model or api-key');

            return self::FAILURE;
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
