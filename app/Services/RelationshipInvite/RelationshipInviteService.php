<?php

namespace App\Services\RelationshipInvite;

use App\Models\Sales\Customer\Customer;
use App\Models\Relationship\Relationship;
use App\Models\Relationship\RelationshipInvite;
use App\Models\Role;
use App\Models\User\User;
use App\Services\Contract\ContractRepository;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\RelationshipInvite\Dto\CreateContractJointOwnerDto;
use App\Services\RelationshipInvite\Dto\SaveRelationshipInviteDto;
use App\Services\RelationshipInvite\Exceptions\UnableToFindContractException;
use App\Services\RelationshipInvite\Exceptions\UnableToSetJointOwnerException;
use Carbon\Carbon;
use Psr\SimpleCache\CacheInterface;

/**
 * Class RelationshipInviteService
 *
 * @package App\Services\RelationshipInvite
 */
class RelationshipInviteService
{
    public function __construct(
        private DynamicsCrmClient $dynamicsCrmClient,
        private ContractRepository $contractRepository,
        private CacheInterface $cache
    ) {
    }

    /**
     * @param string                    $accountNumber
     * @param SaveRelationshipInviteDto $dto
     *
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws UnableToFindContractException
     * @throws UnableToSetJointOwnerException
     */
    public function store(string $accountNumber, SaveRelationshipInviteDto $dto): void
    {
        $relationshipInvite = RelationshipInvite::create([
            'account_number' => $accountNumber,
            'owner_id' => $dto->owner->id,
            'role' => $dto->role,
            'first_name' => $dto->firstName,
            'last_name' => $dto->lastName,
            'phone' => $dto->phone,
            'birth_date' => $dto->birthDate,
        ]);

        if ($user = User::where(['phone' => $dto->phone])->first()) {
            $this->accept($user, $relationshipInvite);
        }
    }

    /**
     * @throws UnableToFindContractException
     * @throws BadRequestException
     * @throws UnableToSetJointOwnerException
     * @throws NotFoundException
     */
    public function storeFromCustomer(string $personalAccount, Customer $customer, User $owner): void
    {
        $saveRelationshipInviteDto = new SaveRelationshipInviteDto(
            firstName: $customer->getFirstName(),
            lastName: $customer->getLastName(),
            phone: $customer->getPhone(),
            birthDate: new Carbon($customer->getBirthDate()),
            role: Role::tenant(),
            owner: $owner,
        );
        $this->store($personalAccount, $saveRelationshipInviteDto);
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     * @throws UnableToSetJointOwnerException
     * @throws UnableToFindContractException
     */
    public function accept(User $user, RelationshipInvite $relationshipInvite): void
    {
        $createContractJointOwnerDto = new CreateContractJointOwnerDto(
            customerId: $user->crm_id,
            lastName: $user->last_name,
            firstName: $user->first_name,
            middleName: $user->middle_name,
            phone: $user->phone,
            birthDate: $user->birth_date,
            email: $user->email,
            role: $relationshipInvite->role,
        );

        $contractId = $this->getContractId($relationshipInvite);


        if (!$this->getCached($contractId . "_jo_lock")) {
            $this->setCached($contractId . "_jo_lock", true);
            $jointOwner = $this->dynamicsCrmClient->setContractJointOwner(
                $contractId,
                $createContractJointOwnerDto,
            );
        } else {
            return;
        }

        if (!isset($jointOwner['jointOwnerId'])) {
            throw new UnableToSetJointOwnerException();
        }

        $user->relationships()->firstOrCreate([
            'account_number' => $relationshipInvite->account_number,
            'role' => $relationshipInvite->role,
            'joint_owner_id' => $jointOwner['jointOwnerId'],
        ]);

        $relationshipInvite->accepted_by = $user->id;
        $relationshipInvite->accepted_at = Carbon::now();
        $relationshipInvite->save();
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     * @throws UnableToFindContractException
     */
    public function delete(string $accountNumber, int $id): void
    {
        /** @var RelationshipInvite $relationshipInvite */
        $relationshipInvite = RelationshipInvite::find($id);
        if ($relationshipInvite) {
            /** @var User $user */
            $user = User::firstWhere(['phone' => $relationshipInvite->phone]);
            if ($user) {
                /** @var Relationship $relationship */
                $relationship = $user->relationships()->byAccountNumber($accountNumber)->first();

                $this->dynamicsCrmClient->deleteContractJointOwner(
                    $this->getContractId($relationshipInvite),
                    $relationship->joint_owner_id,
                );

                $relationship->delete();
            }

            $relationshipInvite->delete();
        }
    }

    /**
     * @param array<RelationshipInvite> $invitesToRemove
     *
     * @return void
     */
    public function deleteRelations(array $invitesToRemove): void
    {
        foreach ($invitesToRemove as $inviteToRemove) {
            $user = User::firstWhere(['phone' => $inviteToRemove->phone]);
            if ($user) {
                /** @var Relationship $relationship */
                $relationship = $user->relationships()->byAccountNumber($inviteToRemove->account_number)->first();
                $relationship->delete();
            }

            $inviteToRemove->delete();
        }
    }

    /**
     * @throws UnableToFindContractException
     * @throws NotFoundException
     * @throws BadRequestException
     */
    private function getContractId(RelationshipInvite $relationshipInvite): string
    {
        $contract = $this->contractRepository->getByAccountNumber(
            $relationshipInvite->account_number,
            $relationshipInvite->owner->crm_id,
        );
        if (!$contract) {
            throw new UnableToFindContractException();
        }

        return $contract->getId();
    }

    private function setCached(string $key, mixed $data): void
    {
        $this->cache->set($this->getCacheKey($key), $data, 60);
    }

    private function getCacheKey(string $key): string
    {
        return "$key";
    }

    private function getCached(string $key): mixed
    {
        return $this->cache->get($this->getCacheKey($key), []);
    }
}
