<?php

namespace App\Console;

use App\Console\Commands\ChangeAdminEmailCommand;
use App\Console\Commands\ChangeAdminPasswordCommand;
use App\Console\Commands\CreateAdminCommand;
use App\Console\Commands\DeleteAdminCommand;
use App\Console\Commands\SyncData;
use App\Server;
use App\Services\Crawler;
use Carbon\Carbon;
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
        CreateAdminCommand::class,
        DeleteAdminCommand::class,
        ChangeAdminPasswordCommand::class,
        ChangeAdminEmailCommand::class,
        SyncData::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command('sync:data')->name('dailySync')->withoutOverlapping()->daily();;
    }
}
