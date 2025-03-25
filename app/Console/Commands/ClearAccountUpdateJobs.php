<?php

namespace App\Console\Commands;

use App\Models\Job;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use Illuminate\Console\Command;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class ClearAccountUpdateJobs
 *
 * @package App\Console\Commands
 */
class ClearAccountUpdateJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account-jobs:clear';

    /**
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    public function handle(): void
    {
        $countUpdateAccounts = Job::where('queue', 'update_accounts')->count();

        if ($countUpdateAccounts > 100000) {
            Job::where('queue', 'update_accounts')->delete();
            $this->info('Записи update_accounts успешно удалены.');
        } else {
            $this->info('Количество записей не превышает 150000');
        }

        $countUpdateAccountNumbers = Job::where('queue', 'update_account_numbers')->count();

        if ($countUpdateAccountNumbers > 100000) {
            Job::where('queue', 'update_account_numbers')->delete();
            $this->info('Записи update_accounts успешно удалены.');
        } else {
            $this->info('Количество записей не превышает 150000');
        }
    }
}
