<?php

namespace App\Console;

use App\Console\Commands\DeleteOldUsers;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
         Commands\GeneratePermissionsFromPolicies::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        if (config('app.is_demo')){
            $schedule->command('migrate:fresh --seed')->everyFifteenMinutes();
            $schedule->command('image:seed')->everyFifteenMinutes();
        }

        $schedule->call(function () {
            $folders = ['delivery_notes', 'purchase_tickets', 'warehouse_transfers'];
        
            foreach ($folders as $folder) {
                $files = glob(public_path() . "/pdf/$folder/*");
                foreach ($files as $file) {
                    if (is_file($file) && filemtime($file) < strtotime('-1 day')) {
                        unlink($file);
                    }
                }
            }
        })->daily();

        $schedule->command('sync:webshop')->everyFifteenMinutes();

        // Daily full sync with PDF download (overnight)
        $schedule->command('billingo:sync --type=all --days=1 --download-pdf --skip-errors')
            ->dailyAt('03:00');

        // Hourly payment status updates for recent bills
        $schedule->command('billingo:sync --update-status --days=2 --limit=20')
            ->hourly()
            ->between('8:00', '18:00');

        // Weekly full archive sync (Sundays)
        $schedule->command('billingo:sync --type=all --days=30 --force')
            ->sundays()
            ->at('04:00');

        $schedule->command('performance:record-daily')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
