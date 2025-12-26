<?php

namespace App\Console;

use App\Jobs\CreateTaskOccurrences;
use App\Jobs\MarkOverdueOccurrencePayments;
use App\Jobs\SendUpcomingPaymentReminders;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *  run command php artisan schedule:work
     */
    protected function schedule(Schedule $schedule): void
    {

        $schedule->job(new MarkOverdueOccurrencePayments())
            ->everySixHours()
            ->withoutOverlapping();

        $schedule->job(new SendUpcomingPaymentReminders())
            ->daily()
            ->withoutOverlapping();

        $schedule->job(new CreateTaskOccurrences())
            // ->everyTenSeconds()
            ->daily()
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
