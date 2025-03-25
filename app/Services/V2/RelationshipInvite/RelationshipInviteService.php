<?php

namespace App\Services\V2\RelationshipInvite;

use App\Models\Contract\ContractType;
use App\Models\User\User;
use App\Models\V2\DescriptionOfRoles;
use App\Notifications\SendRelationshipSms;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\RelationshipInvite\Dto\SaveRelationshipInviteDto;
use App\Services\V2\RelationshipInvite\Exception\UserHasInvationException;
use Carbon\Carbon;
use Exception;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Collection;

/**
 * Class RelationshipInviteService
 *
 * @package App\Services\V2\RelationshipInvite
 */
class RelationshipInviteService
{
    public function __construct(
        private readonly DynamicsCrmClient $dynamicsCrmClient,
    ) {
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function getRelationshipInvites(User $user, string $accountNumber): array
    {
        // phpcs:disable
        $descriptionOfRoles = collect(DescriptionOfRoles::all()->toArray());
        $crmContractList = $this->dynamicsCrmClient->getContractsByType(ContractType::account(), $user->crm_id)["contractList"];

        $contracts = new Collection($crmContractList);
        $contract = $contracts->filter(function ($contractItem) use ($accountNumber) {
            return (int)$contractItem["personalAccount"] === (int)$accountNumber;
        })->first();

        if (empty($contract)) {
            throw new NotFoundException;
        }

        $jointOwners = new Collection($contract["jointOwners"]);

        return $jointOwners->filter(function ($jointOwner) use ($user) {
            return in_array($jointOwner["roleCode"]["code"], [5,6,7]) &&
                $jointOwner["id"] !== $user->crm_id &&
                $jointOwner["customerType"]["code"] !== "1";
        })->map(function ($jointOwner) use ($descriptionOfRoles) {
            $jointOwner["roleDescription"] = $descriptionOfRoles->where("role_code", "=", $jointOwner["roleCode"]["code"])->first();

            return $jointOwner;
        })->toArray();
        // phpcs:enable
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function deleteRelationshipInvite(User $user, string $accountNumber, string $jointOwner): void
    {
        // phpcs:disable
        $crmContractList = $this->dynamicsCrmClient->getContractsByType(ContractType::account(), $user->crm_id)['contractList'];
        // phpcs:enable

        $contracts = new Collection($crmContractList);
        $contract = $contracts->filter(function ($contractItem) use ($accountNumber) {
            return (int)$contractItem['personalAccount'] === (int)$accountNumber;
        })->first();

        try {
            $this->dynamicsCrmClient->deleteContractJointOwner($contract["id"], $jointOwner);
        } catch (Exception) {
            throw new NotFoundException;
        }
    }

    /**
     * @throws UserHasInvationException
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function store(User $user, string $accountNumber, $request): void
    {
        // phpcs:disable
        $crmContractList = $this->dynamicsCrmClient->getContractsByType(ContractType::account(), $user->crm_id)["contractList"];
        // phpcs:enable

        $contracts = new Collection($crmContractList);
        $contract = $contracts->filter(function ($contractItem) use ($accountNumber) {
            return $contractItem["personalAccount"] === $accountNumber;
        })->first();

        if (empty($contract)) {
            throw new NotFoundException;
        }

        $jointOwners = new Collection($contract["jointOwners"]);

        $thisJointOwner = $jointOwners->filter(function ($jointOwner) use ($request) {
            return isset($jointOwner["phone"]) &&
                (int)$jointOwner["phone"] === (int)$request->get("phone") &&
                $jointOwner["customerType"]["code"] !== "1";
        })->all();

        if (!empty($thisJointOwner)) {
            throw new UserHasInvationException;
        }

        $crmUsers = $this->dynamicsCrmClient->getCustomerByPhone((int)$request->get("phone"))["customerList"];

        $role = [
            'JointOwner' => [
                'roleCode' => [
                    'code' => $request->get('role')
                ]
            ]
        ];

        if (count($crmUsers) === 0) {
            $jointOwner = [
                'JointOwner' => [
                    'LastName' => $request->get('last_name'),
                    'FirstName' => $request->get('first_name'),
                    'Phone' => (int)$request->get('phone'),
                    'BirthDate' => Carbon::parse($request->get('birth_date'))->toDateString(),
                    'roleCode' => [
                        'code' => $request->get('role')
                    ],
                ]
            ];

            $this->dynamicsCrmClient->createContractJointOwnerFromArray($contract['id'], $jointOwner);
        } elseif (count($crmUsers) === 1) {
            $this->dynamicsCrmClient->updateContractJointOwnerFromArray($contract['id'], $crmUsers[0]['id'], $role);
        } elseif (count($crmUsers) > 1) {
            $userFound = false;

            foreach ($crmUsers as $crmUser) {
                if ((int)$crmUser['phone'] === (int)$request->get('phone') &&
                    strtolower($crmUser['firstName']) === strtolower($request->get('first_name')) &&
                    strtolower($crmUser['lastName']) === strtolower($request->get('last_name')) &&
                    new Carbon($crmUser['birthDate']) == new Carbon($request->input('birth_date'))) {
                    $userFound = true;
                    $this->dynamicsCrmClient->updateContractJointOwnerFromArray($contract['id'], $crmUser['id'], $role);
                    break;
                }
            }

            if (!$userFound) {
                $this->dynamicsCrmClient->updateContractJointOwnerFromArray($contract['id'], $crmUsers[0]['id'], $role);
            }
        }
    }

    public function notify(SaveRelationshipInviteDto $inviteDto): void
    {
        $notifiable = new AnonymousNotifiable();
        $notifiable->route('sms', $inviteDto->phone);
        $notifiable->notify(new SendRelationshipSms($inviteDto));
    }
}
