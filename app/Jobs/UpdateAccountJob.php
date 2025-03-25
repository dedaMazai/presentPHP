<?php

namespace App\Jobs;

use App\Models\Account\AccountInfo;
use App\Services\Account\AccountRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateAccountJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 6000;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private string $accountNumber,
    ) {
    }

    public function handle(AccountRepository $repository)
    {

        try {
            $account = $repository->getAccountByNumber($this->accountNumber);

            AccountInfo::updateOrCreate(['account_number' => $account->getNumber()], [
                'realty_type' => $account->getRealtyType(),
                'build_id' => $account->getBuildId(),
                'uk_project_id' => $account->getUkProject()?->id,
                'build_zid' => $account->getBuildZid()?->build_zid,
                'address' => $account->getAddress(),
                'balance' => $account->getBalance(),
                'services_debt' => $account->getServicesDebt(),
                'not_paid_months' => $account->getNotPaidMonths(),
                'is_meter_enter_period_active' => $account->getIsMeterEnterPeriodActive(),
                'project_crm_1c_id' => $account->getUkProject()?->crm_1c_id,
                'meter_enter_period_start' => $account->getMeterEnterPeriod()?->getStartDate(),
                'meter_enter_period_end' => $account->getMeterEnterPeriod()?->getEndDate(),
                'address_id' => $account->getAddressId(),
                'service_seller_id' => $account->getServiceSeller()?->getId(),
                'address_number' => $account->getAddressNumber(),
                'project_name' => $account->getUkProject()?->name,
                'project_id' => $account->getUkProject()?->id,
                'uk_emergency_claim_phone' => $account->getUkEmergencyClaimPhone(),
                'classifier_uk_id' => $account->getClassifierUKId()
            ]);
        } catch (Exception $exception) {
            logger()->debug("UpdateAccount $this->accountNumber with error " . $exception->getMessage());
        }
    }
}
