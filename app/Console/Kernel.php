<?php

namespace App\Console;

use App\Models\Presence;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        // $schedule->call(function () {
        //     $absensi = Presence::where('date', Carbon::now()->toDateString())->get();
        //     foreach($absensi as $data) {
        //         if(Carbon::now()->gte(Carbon::parse($data->end_time))) {
        //             $data->status = 'Alpa';
        //             $data->save();
        //         }
        //     }
        // })->dailyAt('23:59');
        
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
