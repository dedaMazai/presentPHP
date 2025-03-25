<?php

namespace App\Services\Manager;

use App\Http\Api\External\V1\Requests\Sales\ManagerContactsRequest;
use App\Models\Feedback\Feedback;
use App\Models\Sales\Demand\DemandStatus;
use App\Models\Sales\ManagerObjectType;
use App\Models\User\User;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\Feedback\Dto\SaveFeedbackDto;
use App\Services\Manager\Dto\OwnerObjectDto;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

/**
 * Class FeedbackService
 *
 * @package App\Services\Feedback
 */
class ManagerService
{
    public function __construct(
        private DynamicsCrmClient $dynamicsCrmClient,
    ) {
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function getManagerContacts(User $user, ManagerContactsRequest $request): array
    {
        $managers = [];
        $demandList = [];

        $type = $request->get('type');
        $id = $request->get('id');

        if ($type == ManagerObjectType::demand()->value) {
            $demand = $this->dynamicsCrmClient->getDemandById($id, $user);

            if (!isset($demand['ownerObject'])) {
                return [];
            }

            if ($demand['ownerObject'] != null) {
                $managers[] = new OwnerObjectDto(
                    name: $demand['ownerObject']['lastName'] . ' ' .
                    $demand['ownerObject']['firstName'] . ' ' .
                    ($demand['ownerObject']['middleName'] ?? ''),
                    phone: $demand['ownerObject']['phone'] ?? '',
                    email: $demand['ownerObject']['email'] ?? '',
                    type: 'sale'
                );
            }

            $demands = $this->dynamicsCrmClient->getDemandsByStatus($user, DemandStatus::open());

            foreach ($demands['demandList'] as $demand) {
                if ($demand['demandMainId'] ?? null == $id) {
                    $demandList[] = $demand;
                }
            }

            if (count($demandList) > 1) {
                $demand = $demandList[0];

                foreach ($demandList as $demandFromList) {
                    if (Carbon::parse($demandFromList['modifiedOn']) > Carbon::parse($demand['modifiedOn'])) {
                        $demand = $demandFromList;
                    }
                }

                $managers[] = new OwnerObjectDto(
                    name: $demand['ownerObject']['lastName'] . ' ' .
                    $demand['ownerObject']['firstName'] . ' ' .
                    ($demand['ownerObject']['middleName'] ?? ''),
                    phone: $demand['ownerObject']['phone'] ?? '',
                    email: $demand['ownerObject']['email'] ?? '',
                    type: 'mortgage'
                );
            } elseif (count($demandList) == 1) {
                $demand = $demandList[0];

                $managers[] = new OwnerObjectDto(
                    name: $demand['ownerObject']['lastName'] . ' ' .
                    $demand['ownerObject']['firstName'] . ' ' .
                    ($demand['ownerObject']['middleName'] ?? ''),
                    phone: $demand['ownerObject']['phone'] ?? '',
                    email: $demand['ownerObject']['email'] ?? '',
                    type: 'mortgage'
                );
            }
        }

        if ($type == ManagerObjectType::contract()->value) {
            $contract = $this->dynamicsCrmClient->getContractById($id);

            if ($contract['ownerObject'] != null) {
                $managers[] = new OwnerObjectDto(
                    name: $contract['ownerObject']['lastName'] . ' ' .
                    $contract['ownerObject']['firstName'] . ' ' .
                    ($demand['ownerObject']['middleName'] ?? ''),
                    phone: $demand['ownerObject']['phone'] ?? '',
                    email: $demand['ownerObject']['email'] ?? '',
                    type: 'sale'
                );
            }

            if (($contract['ownerMortgage'] ?? null) != null) {
                $managers[] = new OwnerObjectDto(
                    name: $contract['ownerObject']['lastName'] . ' ' .
                    $contract['ownerObject']['firstName'] . ' ' .
                    ($demand['ownerObject']['middleName'] ?? ''),
                    phone: $demand['ownerObject']['phone'] ?? '',
                    email: $demand['ownerObject']['email'] ?? '',
                    type: 'mortgage'
                );
            }
        }

        return $managers;
    }
}
