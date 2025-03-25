<?php

namespace App\Services\Account;

use App\Models\Account\Account;
use App\Models\Account\AccountNumbers;
use App\Models\Account\AccountRealtyType;
use App\Models\Account\AccountServiceSeller;
use App\Models\Building\Building;
use App\Models\Receipt\Receipt;
use App\Models\Role;
use App\Models\UkProject;
use App\Models\User\User;
use App\Services\Crm\CrmClient;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\Meter\MeterEnterPeriodRepository;
use App\Services\Receipt\ReceiptRepository;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class AccountRepository
 *
 * @package App\Services\Account
 */
class AccountRepository
{
    public function __construct(
        private readonly CrmClient                  $crmClient,
        private readonly MeterEnterPeriodRepository $meterEnterPeriodRepository,
        private readonly ReceiptRepository          $receiptRepository,
    ) {
    }

    /**
     * @return Account[]
     * @throws InvalidArgumentException
     * @throws ValidationException
     */
    public function getAccounts(User $user): array
    {
        $data = $this->crmClient->getAccounts($user);

        $accounts = [];

        foreach ($data as $account) {
            $accounts[] = $this->makeAccount($account);
        }

        foreach ($user->relationships as $relationship) {
            if ($relationship->role->equals(Role::tenant())) {
                $accounts[] = $this->getAccountByNumber($relationship->account_number);
            }
        }

        return $accounts;
    }

    /**
     * @throws InvalidArgumentException
     * @throws ValidationException
     */
    public function getAccountByNumber(?string $accountNumber): ?Account
    {
        if ($accountNumber !== null) {
            $data = $this->crmClient->getAccountByNumber($accountNumber);

            if ($data) {
                return $this->makeAccount($data[0]);
            }
        }

        return null;
    }

    /**
     * @throws InvalidArgumentException
     * @throws ValidationException
     */
    public function getAccountsByNumbersFromDb(string $userId): array
    {
        $accounts = AccountNumbers::with('accountInfo')
            ->where('user_id', $userId)
            ->whereHas('accountInfo')
            ->get();

        foreach ($accounts as $account) {
            $meterEnterPeriod = null;

            if ($account->accountInfo->meter_enter_period_start && $account->accountInfo->meter_enter_period_end) {
                $meterEnterPeriod = [
                    'start_date' => $account->accountInfo->meter_enter_period_start,
                    'end_date' => $account->accountInfo->meter_enter_period_end
                ];
            }

            $accountsResult[] = [
                'number' => $account->account_number,
                'role' => $account->role,
                'realty_type' => $account->accountInfo->realty_type->value,
                'address' => $account->accountInfo->address,
                'address_id' => $account->accountInfo->address_id,
                'balance' => intval($account->accountInfo->balance),
                'services_debt' => intval($account->accountInfo->services_debt),
                'service_seller_id' => $account->accountInfo->service_seller_id,
                'not_paid_months' => intval($account->accountInfo->not_paid_months),
                'is_meter_enter_period_active' => boolval($account->accountInfo->is_meter_enter_period_active),
                'project_id' => intval($account->accountInfo->project_id),
                'project_name' => $account->accountInfo->project_name,
                'project_crm_1c_id' => $account->accountInfo->project_crm_1c_id,
                'meter_enter_period' => $meterEnterPeriod,
                'build_id' => $account->accountInfo->build_id,
                'build_zid' => $account->accountInfo->build_zid,
                'address_number' => $account->accountInfo->address_number,
                'uk_emergency_claim_phone' => $account->accountInfo->uk_emergency_claim_phone ?? '+74952121060',
                'classifier_uk_id' => $account->accountInfo->classifier_uk_id,
                'uk_project_id' => $account->accountInfo->uk_project_id,
            ];
        }

        if (!isset($accountsResult)) {
            return [];
        }

        $groupedNumbers = collect($accountsResult)->groupBy(function ($item, $key) {
            return $item['classifier_uk_id'];
        });

        $accountsGroup = [];

        foreach ($groupedNumbers as $key => $projects) {
            $accountsNumbers = [];
            $lastAmount = 0;
            $amountsWithMinus = 0;
            foreach ($projects as $project) {
                $accountsNumbers[] = [
                    'number' => $project['number'],
                    'amount' => abs(min($project['balance'], 0))
                ];

                $amountsWithMinus += $project['balance'] < 0 ? 1 : 0;
                $lastAmount += abs(min($project['balance'], 0));
            }

            if (count($accountsNumbers) === 1) {
                $button = 'hide';
            } elseif ($amountsWithMinus === 1 && count($accountsNumbers) > 1) {
                $button = 'inactive';
            } elseif ($amountsWithMinus > 1 && count($accountsNumbers) > 1) {
                $button = 'active';
            }

            $button = 'hide';

            $ukProject = UkProject::byCrm1CId($key)->first();

            /** @var UkProject $ukProject */
            $accountsGroup[] = [
                'project_id' => $ukProject?->id,
                'project_name' => $ukProject?->name,
                'accounts_numbers' => $accountsNumbers,
//                'payment_button' => $button ?? null, закомментировано для задачи PION-3511
                'payment_button' => 'hide',
                'total_amount' => $lastAmount,
            ];
        }

        return ['accounts' => $accountsResult, 'accountsGroup' => $accountsGroup];
    }


    /**
     * @throws InvalidArgumentException
     * @throws ValidationException
     */
    public function getAccountsByNumbers(array $accountNumbers): array
    {
        $accounts = [];

        foreach ($accountNumbers as $accountNumber) {
            $accounts[] = $this->getAccountByNumber($accountNumber);
        }

        $groupedNumbers = collect($accounts)->groupBy(function ($item, $key) {
            /** @var Account $item */
            return $item->getClassifierUKId();
        });

        $accountsGroup = [];

        foreach ($groupedNumbers as $key => $projects) {
            $accountsNumbers = [];
            $lastAmount = 0;
            $amountsWithMinus = 0;
            foreach ($projects as $project) {
                /** @var Account $project */
                $accountsNumbers[] = [
                    'number' => $project->getNumber(),
                    'amount' => abs(min($project->getBalance(), 0))
                ];

                $amountsWithMinus += $project->getBalance() < 0 ? 1 : 0;
                $lastAmount += abs(min($project->getBalance(), 0));
            }

            if (count($accountsNumbers) === 1) {
                $button = 'hide';
            } elseif ($amountsWithMinus === 1 && count($accountsNumbers) > 1) {
                $button = 'inactive';
            } elseif ($amountsWithMinus > 1 && count($accountsNumbers) > 1) {
                $button = 'active';
            }

            $button = 'hide';

            $ukProject = UkProject::byCrm1CId($key)->first();

            /** @var UkProject $ukProject */
            $accountsGroup[] = [
                'project_id' => $ukProject?->id,
                'project_name' => $ukProject?->name,
                'accounts_numbers' => $accountsNumbers,
//                'payment_button' => $button ?? null, закомментировано для задачи PION-3511
                'payment_button' => 'hide',
                'total_amount' => $lastAmount,
            ];
        }

        return ['accounts' => $accounts, 'accountsGroup' => $accountsGroup];
    }

    /**
     * @throws InvalidArgumentException
     * @throws ValidationException
     */
    private function makeAccount(array $data): Account
    {
        $meterEnterPeriod = $this->meterEnterPeriodRepository->getByBuildingAddressId($data['address']['zid']);
        if ($meterEnterPeriod) {
            $isMeterEnterPeriodActive = (new Carbon())->betweenIncluded(
                $meterEnterPeriod->getStartDate(),
                $meterEnterPeriod->getEndDate()
            );
        } else {
            $isMeterEnterPeriodActive = false;
        }

        if (isset($data['address']['zid'])) {
            $building = Building::where('build_zid', $data['address']['zid'])->first();
        }

        return new Account(
            number: $data['account_number'],
            realtyType: AccountRealtyType::from($data['realty_type']['code']),
            address: $data['address']['name'],
            addressId: $data['address']['id'],
            address1cId: $data['address']['zid'],
            balance: $data['account_balance'],
            servicesDebt: $data['services_debt'],
            serviceSeller: new AccountServiceSeller(
                $data['accountServiceSeller']['id'],
                $data['accountServiceSeller']['name']
            ),
            notPaidMonths: $this->getNotPaidMonths($data['account_number']),
            isMeterEnterPeriodActive: $isMeterEnterPeriodActive,
            meterEnterPeriod: $meterEnterPeriod,
            floor: $data['floor'] ?? null,
            addressNumber: $data['addressNumber'] ?? null,
            rooms: $data['rooms'] ?? null,
            totalArea: $data['spaceBti'] ?? null,
            livingArea: $data['spaceLiving'] ?? null,
            metersCount: $data['counters'] ?? null,
            ukProject: UkProject::byCrm1CId($data['classifierUKId'] ?? null)->first(),
            buildZid: Building::byBuildZid($data['address']['zid'] ?? null)->first(),
            buildId: $building->id ?? null,
            ukEmergencyClaimPhone: UkProject::byCrm1CId($data['classifierUKId'] ?? null)
                ->first()?->uk_emergency_claim_phone,
            classifierUKId: $data['classifierUKId'] ?? null
        );
    }

    /**
     */
    private function getNotPaidMonths(string $accountNumber): int
    {
        /** @var Receipt[] $receipts */
        try {
            $receipts = $this->receiptRepository->getReceipts(
                $accountNumber,
                Carbon::today()->startOfYear()->subYears(10),
                Carbon::today(),
            );
        } catch (\Throwable $throwable) {
            $receipts = [];
        }

        $notPaidReceipts = 0;
        foreach ($receipts as $receipt) {
            if ($receipt->getPaid() !== $receipt->getTotal()) {
                $notPaidReceipts++;
            }
        }

        return $notPaidReceipts;
    }
}
