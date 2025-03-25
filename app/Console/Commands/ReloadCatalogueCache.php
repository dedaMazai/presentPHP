<?php

namespace App\Console\Commands;

use App\Services\Claim\ClaimCatalogueService;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class ReloadCatalogueCache
 *
 * @package App\Console\Commands
 */
class ReloadCatalogueCache extends Command
{
    protected $signature = 'catalogue:reload-cache';

    /**
     * @param ClaimCatalogueService $catalogueService
     */
    public function handle(ClaimCatalogueService $catalogueService): void
    {
        if (Cache::get('catalogue:reload-cache:attempts') >= 5) {
            $this->info('Catalogue cache not reloaded.');
        } else {
            try {
                $catalogueService->reloadTreeCache();
                $catalogueService->reloadPopularServicesCache();
            } catch (\Exception $exception) {
                Artisan::call('catalogue:reload-cache');
                Cache::increment('catalogue:reload-cache:attempts');
                logger()->debug('ReloadCatalogueCache::handle', ['error']);
                $this->info('Catalogue cache reloaded with exception.');
            }
            Cache::put('catalogue:reload-cache:attempts', 0);
        }


        $this->info('Catalogue cache were reloaded.');
    }
}
