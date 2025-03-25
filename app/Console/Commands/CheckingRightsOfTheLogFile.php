<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Class CheckingRightsOfTheLogFile
 *
 * @package App\Console\Commands
 */
class CheckingRightsOfTheLogFile extends Command
{
    protected $signature = 'owners:check-owners';

    public function handle(): void
    {
        $date = now()->format('Y-m-d');
        $filePath = 'laravel-' . $date . '.log';
        $path =  storage_path('logs/' . $filePath);
        chown($path, 'www-data');
        chgrp($path, 'www-data');
    }
}
