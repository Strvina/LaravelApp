<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Zakazuje recurring tasks command da se izvršava svaki dan u ponoć
        $schedule->command('app:generate-recurring-tasks')->daily();

        // Ako želiš, možeš za testiranje da ga pokrećeš svakih 5 minuta
        //$schedule->command('app:generate-recurring-tasks')->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        // Automatski registruje sve komande iz foldera app/Console/Commands
        $this->load(__DIR__.'/Commands');

        // Možeš dodati i dodatne console rute ako želiš
        require base_path('routes/console.php');
    }
}
