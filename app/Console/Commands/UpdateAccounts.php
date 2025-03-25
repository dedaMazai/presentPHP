<?php

namespace App\Console\Commands;

use App\Jobs\UpdateAccountJob;
use App\Models\Account\AccountNumbers;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use Illuminate\Console\Command;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class UpdateAccounts
 *
 * @package App\Console\Commands
 */
class UpdateAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounts:update';
    private string $cacheKey = 'update-accounts-command';

    /**
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    public function handle(CacheInterface $cache): void
    {
        if (!$cache->get($this->cacheKey)) {
            $cache->set(
                $this->cacheKey,
                true,
                now()->addMinute()
            );
        } else {
            $this->info('The command is already running');

            return;
        }

        $accountNumbers = AccountNumbers::all()->pluck('account_number')
            ->toArray();

        foreach ($accountNumbers as $accountNumber) {
            UpdateAccountJob::dispatch($accountNumber)->onQueue('update_accounts');
        }

        $this->info('Accounts updated.');
    }
}
