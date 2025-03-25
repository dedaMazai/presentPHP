<?php

namespace App\Services\Claim;

use App\Models\Claim\Claim;
use App\Models\User\User;
use App\Services\Contract\ContractRepository;
use App\Services\Contract\ContractService;

/**
 * Class ClaimMessageService
 *
 * @package App\Services\Claim
 */
class LastClaimService
{
    public function __construct(
        private ContractRepository $contractRepository,
        private ContractService $contractService,
        private ClaimRepository $claimRepository
    ) {
    }

    public function getLastClaimByAccounts(User $user)
    {
        $accountsNumbers = $this->contractRepository->getPersonalAccountByContracts($user->crm_id);
//        $accountsNumbers = $this->contractService->getAccountsFromContracts($contracts);
        /** @var Claim $lastClaim */
        $lastClaim = $this->claimRepository->getLastClaims($accountsNumbers);

//        $accounts = $this->accountRepository->getAccountsByNumbers($accountsNumbers);
//        $result = null;
//        $account_elements = [];
//
//        if (!$accounts) {
//            return null;
//        }
//
//        foreach ($accounts as $account) {
//            $claim_element = null;
//            $claim = $this->claimRepository->getAllByLastCreated($account->getNumber());
//
//            foreach ($claim as $claim_el) {
//                if ($claim_element==null??($claim_element?->getModifiedOn()->toDateTimeString()<
//                        $claim_el?->getModifiedOn()->toDateTimeString())&&
//                    ($claim_el->getIsNotReadSMS()||$claim_el->getIsNotReadDocument())
//                ) {
//                    $claim_element = $claim_el;
//                }
//            }
//            if ($claim_element) {
//                $account_elements[] = ['accountNumber' => $account->getNumber(), 'claim' => $claim_element];
//            }
//        }
//
//        foreach ($account_elements as $value) {
//            if (!$result) {
//                $result = $value;
//            } elseif ($result['claim']->getModifiedOn()<$value['claim']->getModifiedOn()) {
//                $result = $value;
//            }
//        }
        if ($lastClaim == null) {
            return null;
        }
        return ['claim' => $lastClaim, 'accountNumber' => $lastClaim?->getAccountNumber()];
    }
}
