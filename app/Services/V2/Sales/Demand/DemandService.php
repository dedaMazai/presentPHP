<?php

namespace App\Services\V2\Sales\Demand;

use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\V2\Sales\Demand\Dto\Deponent\DeponentFizIdDto;
use App\Services\V2\Sales\Demand\Dto\Deponent\DeponentUrIdDto;

class DemandService
{
    public function __construct(
        protected DynamicsCrmClient $dynamicsCrmClient
    ) {
    }

    public function getDeponent($object, string $id)
    {
        $depositorDocument = [];
        $depositorInfo = null;
        $ownerId = null;

        $checkIn = null;
        foreach ($object->getJointOwners() as $jointOwner) {
            if (!empty($object->getDepositorFizId()) && $jointOwner->getContactId() == $object->getDepositorFizId()) {
                $checkIn = $jointOwner;
                $depositorInfo = new DeponentFizIdDto(
                    $object->getDepositorFizId(),
                    $jointOwner->getId(),
                    $jointOwner->getFirstName(),
                    $jointOwner->getLastName(),
                    $jointOwner->getMiddleName()
                );
            }

            if (!empty($object->getDepositorUrId())) {
                $depositorInfo = new DeponentUrIdDto(
                    $object->getDepositorUrId(),
                    $jointOwner->getId(),
                    $jointOwner->getName()
                );
            }
        }

        if ($checkIn) {
            $documents = $this->getJointOwnerDocuments($checkIn->getId());
            $ownerId = $checkIn->getId();

            if (!empty($documents['documentList'])) {
                foreach ($documents['documentList'] as $document) {
                    if ($document['documentType']['code'] == "40015") {
                        $doc = $document;
                    }
                }
            }
        }

        if (!isset($doc)) {
            $depositorDocument['document_info'] = null;
        } else {
            $depositorDocument['document_info'] = $doc;
        }

        if ($depositorInfo instanceof DeponentUrIdDto) {
            $depositorDocument['document_info']['id'] = $depositorInfo->id ?? null;
            $depositorDocument['document_info']['joint_owner_id'] = $depositorInfo->joint_owner_id  ?? null;
            $depositorDocument['document_info']['name'] = $depositorInfo->name ?? null;
        } elseif ($depositorInfo instanceof DeponentFizIdDto) {
            $depositorDocument['document_info']['id'] = $depositorInfo->id ?? null;
            $depositorDocument['document_info']['joint_owner_id'] = $depositorInfo->joint_owner_id  ?? null;
            $depositorDocument['document_info']['fist_name'] = $depositorInfo->first_name ?? null;
            $depositorDocument['document_info']['last_name'] = $depositorInfo->last_name ?? null;
            $depositorDocument['document_info']['middle_name'] = $depositorInfo->middle_name ?? null;
        }

        return [
            'depositorInfo' => $depositorInfo,
            'depositorDocument' => $depositorDocument,
            'ownerId' => $ownerId,
        ];
    }

    public function getJointOwnerDocuments(string $id)
    {
        return $this->dynamicsCrmClient->getJointOwnerDocuments($id);
    }
}
