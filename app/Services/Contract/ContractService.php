<?php

namespace App\Services\Contract;

use App\Models\Contract\Contract;
use App\Models\Relationship\RelationshipInvite;
use App\Models\Role;
use App\Models\Sales\Customer\Customer;
use App\Models\User\User;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\RelationshipInvite\Exceptions\UnableToFindContractException;
use App\Services\RelationshipInvite\Exceptions\UnableToSetJointOwnerException;
use App\Services\RelationshipInvite\RelationshipInviteService;

class ContractService
{
    public function __construct(
        private RelationshipInviteService $relationshipInviteService
    ) {
    }

    /**
     * @param array<Contract> $contracts
     *
     * @return array<string>
     */
    public function getAccountsFromContracts(array $contracts): array
    {
        $accountNumbers = [];
        foreach ($contracts as $contract) {
            $accountNumbers[] = $contract->getPersonalAccount();
        }

        return $accountNumbers;
    }

    /**
     * @param array<Contract> $contracts
     *
     * @return array<string>
     */
    public function getAccountsFromContractsForUser(array $contracts): array
    {
        $accountNumbers = [];
        foreach ($contracts as $contract) {
            $accountNumbers[] = $contract;
        }

        return $accountNumbers;
    }

    /**
     * @throws UnableToFindContractException
     * @throws BadRequestException
     * @throws UnableToSetJointOwnerException
     * @throws NotFoundException
     */
    public function updateRelationsByContracts(array $contracts, User $user): void
    {
        foreach ($contracts as $contract) {
            if ($this->checkContractOwner($contract, $user)) {
                $contractTenants = $this->getTenantsFromContract($contract);
                $this->updateInvitesForTenants($contractTenants, $contract, $user);
                $this->deleteRelationsNotInContract($contract);
            }
        }
    }

    public function checkContractOwner(Contract $contract, User $user): bool
    {
        $contractMembers = $contract->getJointOwners();

        foreach ($contractMembers as $contractMember) {
            if ($contractMember->getId() == $user->crm_id && $contractMember->getRole() === Role::client()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<Customer>
     */
    public function getTenantsFromContract(Contract $contract): array
    {
        $tenants = [];
        $contractMembers = $contract->getJointOwners();

        foreach ($contractMembers as $contractMember) {
            if ($contractMember->getRole() === Role::tenant()) {
                $tenants[] = $contractMember;
            }
        }

        return $tenants;
    }

    /**
     * @param array<Customer> $contractTenants
     *
     * @throws UnableToFindContractException
     * @throws BadRequestException
     * @throws UnableToSetJointOwnerException
     * @throws NotFoundException
     */
    private function updateInvitesForTenants(array $contractTenants, Contract $contract, User $user): void
    {
        foreach ($contractTenants as $tenant) {
            $personalAccount = $contract->getPersonalAccount();
            $dbTenant = User::wherePhone($tenant->getPhone())->first();
            $dbInvite = RelationshipInvite::where([
                'account_number' => $personalAccount,
                'phone' => $tenant->getPhone(),
            ])->first();

            if (!empty($dbTenant) && !empty($dbInvite)) {
                $this->relationshipInviteService->accept($dbTenant, $dbInvite);
            } elseif (empty($dbInvite)) {
                $this->relationshipInviteService->storeFromCustomer(
                    $personalAccount,
                    $tenant,
                    $user
                );
            }
        }
    }

    private function deleteRelationsNotInContract(Contract $contract): void
    {
        $phones = [];
        $tenants = $this->getTenantsFromContract($contract);

        foreach ($tenants as $tenant) {
            $phones[] = $tenant->getPhone();
        }

        $invitesToRemove = RelationshipInvite::where(['account_number' => $contract->getPersonalAccount()])
                                             ->whereNotNull('accepted_by')
                                             ->whereNotIn('phone', $phones)
                                             ->get();

        if ($invitesToRemove->isNotEmpty()) {
            $this->relationshipInviteService->deleteRelations($invitesToRemove);
        }
    }
}
