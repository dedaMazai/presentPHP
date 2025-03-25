<?php

namespace App\Console;

use App\Console\Commands\CheckCrmStatus;
use App\Console\Commands\CheckDemandsPaidBookingStatus;
use App\Console\Commands\CheckFiscalStatus;
use App\Console\Commands\CheckingRightsOfTheLogFile;
use App\Console\Commands\CheckingUsedMemory;
use App\Console\Commands\CheckOrderStatus;
use App\Console\Commands\ClearAccountUpdateJobs;
use App\Console\Commands\ReloadAccountPopularServicesCache;
use App\Console\Commands\ReloadAccountsCatalogueCache;
use App\Console\Commands\ReloadCatalogueCache;
use App\Console\Commands\RemoveOldLogs;
use App\Console\Commands\UpdateAccountNumbers;
use App\Console\Commands\UpdateAccounts;
use App\Console\Commands\UpdateProjectMinPropertyPrice;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(UpdateProjectMinPropertyPrice::class)->dailyAt('02:00');
        $schedule->command(ReloadCatalogueCache::class)->dailyAt('03:30');
        $schedule->command(ReloadAccountsCatalogueCache::class)->dailyAt('04:00');
        $schedule->command(ReloadAccountPopularServicesCache::class)->dailyAt('04:30');
//        $schedule->command(CheckDemandsPaidBookingStatus::class)->everyMinute();
        $schedule->command(CheckCrmStatus::class)->everyFiveMinutes();
        $schedule->command(CheckingRightsOfTheLogFile::class)->everyTenMinutes()->unlessBetween('7:00', '3:00');
        $schedule->command(RemoveOldLogs::class)->dailyAt('09:00');
        $schedule->command(CheckingUsedMemory::class)->everyTwoHours();
        $schedule->command(CheckOrderStatus::class)->everyMinute();
        $schedule->command(CheckFiscalStatus::class)->everyMinute();
        $schedule->command(UpdateAccounts::class)->cron('20 * * * *');
        $schedule->command(UpdateAccountNumbers::class)->cron('15 * * * *');
        $schedule->command(ClearAccountUpdateJobs::class)->dailyAt('03:45');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
