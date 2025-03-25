<?php

namespace App\Console\Commands;

use App\Jobs\ReloadAccountPopularTreeCache;
use App\Jobs\ReloadAccountTreeCache;
use App\Models\Account\AccountInfo;
use App\Services\Claim\ClaimCatalogueService;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use Illuminate\Console\Command;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class ReloadAccountsCatalogueCache
 *
 * @package App\Console\Commands
 */
class ReloadAccountPopularServicesCache extends Command
{
    protected $signature = 'catalogue:reload-account-popular-services';

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws InvalidArgumentException
     */
    public function handle(): void
    {
        $accountNumbers = AccountInfo::all()->pluck('account_number')
            ->toArray();

        foreach ($accountNumbers as $accountNumber) {
            ReloadAccountPopularTreeCache::dispatch($accountNumber)->onQueue('default');
        }

        $this->info('Catalogue cache were reloaded.');
    }
}
