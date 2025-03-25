<?php

namespace App\Console\Commands;

use App\Jobs\UpdateAccountJob;
use App\Jobs\UpdateAccountNumberJob;
use App\Models\Account\AccountInfo;
use App\Models\Project\Project;
use App\Models\Relationship\Relationship;
use App\Models\User\User;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\Project\ProjectService;
use Illuminate\Console\Command;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class UpdateAccountNumbers
 *
 * @package App\Console\Commands
 */
class UpdateAccountNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account-numbers:update';
    private string $cacheKey = 'update-account-numbers-command';

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

        $users = User::all()->map(function ($user) {
            return [
                'id' => $user->id,
                'crm_id' => $user->crm_id,
            ];
        })->toArray();

        foreach ($users as $user) {
            UpdateAccountNumberJob::dispatch($user['id'], $user['crm_id'])->onQueue('update_account_numbers');
        }

        $this->info('Accounts numbers updated.');
    }
}
