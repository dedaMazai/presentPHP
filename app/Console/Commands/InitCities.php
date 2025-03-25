<?php

namespace App\Console\Commands;

use App\Models\City;
use Illuminate\Console\Command;

/**
 * Class InitCities
 *
 * @package App\Console\Commands
 */
class InitCities extends Command
{
    private const MOSCOW = 'Москва';
    private const ST_PETERSBURG = 'Санкт-Петербург';
    private const CITIES = [
        self::MOSCOW,
        self::ST_PETERSBURG
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cities:init';

    public function handle()
    {
        foreach (self::CITIES as $city) {
            if (!City::where('name', $city)->exists()) {
                City::create(['name' => $city]);
            }
        }

        $this->info('Cities were synced');
    }
}
