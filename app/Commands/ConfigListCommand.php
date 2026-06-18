<?php

namespace App\Commands;

use App\Repositories\ConfigRepository;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class ConfigListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'config:list';

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
        $this->newLine();
        $this->table(
            ['Key', 'Value'],
            collect($configRepository->all())
                ->map(fn ($value, $key) => [
                    $key, $value
                ])
                ->values()
        );
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
