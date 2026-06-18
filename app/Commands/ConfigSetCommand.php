<?php

namespace App\Commands;

use App\Repositories\ConfigRepository;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class ConfigSetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'config:set {key} {value}';

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
        ConfigRepository $configRepository
    )
    {
        $configRepository->set(
            $this->argument('key'),
            $this->argument('value')
        );

        $this->info('Configuration saved.');
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
