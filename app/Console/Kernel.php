<?php

namespace App\Console;

use App\Jobs\CreateTaskOccurrences;
use App\Jobs\MarkOverdueOccurrencePayments;
use App\Jobs\SendUpcomingPaymentReminders;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Bus;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *  run command php artisan schedule:work
     */
    protected function schedule(Schedule $schedule): void
    {

        $schedule->call(function (): void {
            Bus::chain([
                new SendUpcomingPaymentReminders(),
                new MarkOverdueOccurrencePayments(),
                new CreateTaskOccurrences(),
            ])->dispatch();
        })
            ->name('daily-ordered-jobs-for-occurrences')
            // TODO: Temp 
            // ->dailyAt('17:00')
            // ->timezone('Asia/Tbilisi')
            // ->everyFiveSeconds()
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
