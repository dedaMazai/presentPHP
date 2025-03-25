<?php

namespace App\Services\Account;

use App\Models\Account\Account;
use App\Models\Account\AccountInfo;
use App\Models\Contract\Contract;
use App\Models\Relationship\Relationship;
use App\Models\Sales\Customer\Customer;
use App\Models\User\User;
use App\Services\Account\Dto\RefillBalanceDto;
use App\Services\Crm\CrmClient;
use Illuminate\Validation\ValidationException;

/**
 * Class AccountService
 *
 * @package App\Services\Account
 */
class AccountService
{
    public function __construct(private CrmClient $crmClient)
    {
    }

    /**
     * @param  Account[]  $accounts
     * @param  Contract[]  $contracts
     */
    public function updateAccounts(User $user, array $accounts, array $contracts): void
    {
        foreach ($accounts as $account) {
            $accountInfo = AccountInfo::where('account_number', $account->getNumber())->first();

            if ($accountInfo === null) {
                AccountInfo::create([
                    'account_number' => $account->getNumber(),
                    'realty_type' => $account->getRealtyType(),
                    'build_id' => $account->getBuildId(),
                    'uk_project_id' => $account->getUkProject()?->id,
                    'build_zid' => $account->getBuildZid()?->build_zid,
                ]);
            } else {
                $accountInfo->update([
                    'realty_type' => $account->getRealtyType(),
                    'build_id' => $account->getBuildId(),
                    'uk_project_id' => $account->getUkProject()?->id,
                    'build_zid' => $account->getBuildZid()?->build_zid,
                ]);
            }

//            AccountInfo::updateOrCreate([
//                'account_number' => $account->getNumber(),
//            ], [
//                'realty_type' => $account->getRealtyType(),
//                'uk_project_id' => $account->getUkProject()?->id,
//                'build_zid' => $account->getBuildZid()?->build_zid,
//                'build_id' => $account->getBuildId(),
//            ]);
        }

//        $accountNumbers = Relationship::pluck('account_number')->toArray();
//        AccountInfo::whereNotIn('account_number', $accountNumbers)->delete();

        $customers = [];

        foreach ($contracts as $contract) {
            $accountNumber = $contract->getPersonalAccount();
            $customers[$accountNumber] = collect($contract->getJointOwners())
                ->first(fn(Customer $customer) => $customer->getContactId() === strtolower($user->crm_id))
                ?->getRole();
        }

        /* @var  Customer $customer */
        foreach ($customers as $accountNumber => $role) {
            Relationship::where('account_number', $accountNumber)
                ->update(['role' => $role?->value]);
        }
    }

    /**
     * @throws ValidationException
     */
    public function refillBalance(RefillBalanceDto $dto, string $paymentMethod): void
    {
        $this->crmClient->refillBalance($dto, $paymentMethod);
    }
}
