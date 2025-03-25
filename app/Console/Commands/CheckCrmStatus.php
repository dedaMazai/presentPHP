<?php

namespace App\Console\Commands;

use App\Mail\CrmFailedMail;
use App\Models\Contract\ContractType;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Class CheckDemandsPaidBookingStatus
 *
 * @package App\Console\Commands
 */
class CheckCrmStatus extends Command
{
    private $email = "andrey.malov@ramax.ru";
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:check-status';

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function handle(
        DynamicsCrmClient $dynamicsCrmClient
    ): void {
        $currentDateTime = new Carbon();

        try {
            $dynamicsCrmClient->getClaimCatalogue();
        } catch (\Exception $e) {
            Mail::to($this->email)->send(new CrmFailedMail());
        }
    }
}
