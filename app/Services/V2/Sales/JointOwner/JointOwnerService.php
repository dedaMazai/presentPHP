<?php

namespace App\Services\V2\Sales\JointOwner;

use App\Models\Sales\Demand\Demand;
use App\Models\User\User;
use App\Services\Document\DocumentRepository;
use App\Services\Sales\Customer\CustomerRepository;
use App\Services\Sales\JointOwner\Dto\GetJointOwnerDto;
use App\Services\Utils\AgeFormatter;
use App\Services\V2\Sales\JointOwner\Dto\JointOwnerDto;
use Carbon\Carbon;

/**
 * Class DemandService
 *
 * @package App\Services\V2\Sales\JointOwner
 */
class JointOwnerService
{
    public function __construct(
        private DocumentRepository $documentRepository,
        private CustomerRepository $customerRepository,
    ) {
    }

    public function getJointOwner($jointOwnerId, $demand, User $user)
    {
        $jointOwner = $this->customerRepository->getById($jointOwnerId);
        $demandJointOwner = null;
        $ageCategory = '';
        $isCustomer = false;
        $isDepositor = false;

        /** @var Demand $demand */
        foreach ($demand->getJointOwners() as $jointOwnerCrm) {
            if ($jointOwnerCrm->getContactId() == $jointOwnerId) {
                $demandJointOwner = $jointOwnerCrm;
                $isDepositor = $demand->getDepositorFizId() == $jointOwner->getId();
                if ($jointOwnerId == $user->crm_id) {
                    $isCustomer = true;
                }
            }
        }

        if ($demandJointOwner) {
            $age = Carbon::parse($jointOwner->getBirthDate())->age;
            $documentTypeCode = $demand->getType()->value;
            $documentTypeCodes = [];

            if ($age >= 18) {
                $ageCategory = 'adult';
            } elseif ($age >= 14 && $age < 18) {
                $ageCategory = 'teen';
            } elseif ($age < 14) {
                $ageCategory = 'child';
            }

            if ($age >= 18 && $documentTypeCode == 1) {
                $documentTypeCodes = [
                    '524491', '524492', '4'
                ];
            } elseif ($age >= 14 && $age < 18 && $documentTypeCode == 1) {
                $documentTypeCodes = [
                    '524491', '524492', '4'
                ];
            } elseif ($age < 14 && $documentTypeCode == 1) {
                $documentTypeCodes = [
                    '40003'
                ];
            } elseif ($age >= 18 && $documentTypeCode == 2) {
                $documentTypeCodes = [
                    '40001', '40002', '4'
                ];
            } elseif ($age >= 14 && $age < 18 && $documentTypeCode == 2) {
                $documentTypeCodes = [
                    '40001', '40002', '40004'
                ];
            } elseif ($age < 14 && $documentTypeCode == 2) {
                $documentTypeCodes = [
                    '40002', '40004'
                ];
            }

            if (($ageCategory == 'teen' || $ageCategory == 'adult') &&  $documentTypeCode == 1) {
                $isRus = true;
            } elseif ($ageCategory == 'child' && $documentTypeCode == 2) {
                $isRus = true;
            } else {
                $isRus = false;
            }
        } else {
            throw new \Exception('Некорректные параметры запроса', 400);
        }

        $client = 0;
        $individualForm = 0;
        $sharedOwnership = 0;
        if ($demandJointOwner->getOwnerType()?->value == 4) {
            foreach ($demand->getJointOwners() as $jointOwner) {
                if ($jointOwner->getOwnerType()->value == 1) {
                    $client += 1;
                } elseif ($jointOwner->getOwnerType()->value == 2) {
                    $sharedOwnership += 1;
                } elseif ($jointOwner->getOwnerType()->value == 5) {
                    $individualForm += 1;
                }
            }
        }

        if (($client == 1 && $individualForm == 1) || ($client > 2 && $sharedOwnership != 0)) {
            $documentTypeCodes[] = 2;
        }

        $documents = [];

        $documents = $this->documentRepository->getDocumentsByCrmIdWithTypeCode(
            $jointOwnerId,
            $documentTypeCodes
        );

        if ($documents == []) {
            foreach ($documentTypeCodes as $documentTypeCode) {
                $data['documentType']['code'] = $documentTypeCode;
                $documents[] = $this->documentRepository->makeDocument($data);
            }
        } elseif (count($documents) != count($documentTypeCodes)) {
            $neededCodes = $documentTypeCodes;
            foreach ($documents as $document) {
                unset($neededCodes[array_search($document->getType()->value, $neededCodes)]);
            }
            foreach ($neededCodes as $neededCode) {
                $data['documentType']['code'] = $neededCode;
                $documents[] = $this->documentRepository->makeDocument($data);
            }
        }

        if ($jointOwner->getPhone() != null) {
            $phone = str_replace('+', '', $jointOwner->getPhone());

            if (str_starts_with($phone, '7')) {
                $phone = substr($phone, 1);
            }
        } else {
            $phone = null;
        }


        $dto = new GetJointOwnerDto(
            id: $jointOwner->getId(),
            jointOwnerId: $demandJointOwner->getId(),
            lastName: $jointOwner->getLastName(),
            firstName: $jointOwner->getFirstName(),
            middleName: $jointOwner->getMiddleName(),
            gender:  $jointOwner->getGender()?->value,
            phone: $phone,
            email: $jointOwner->getEmail(),
            ageCategory: $ageCategory,
            birthDate: $jointOwner->getBirthDate(),
            documents: $documents,
            married: $jointOwner->getMarried(),
            inn: $jointOwner->getInn(),
            snils: $jointOwner->getSnils(),
            part: $demandJointOwner->getPart() ?? null,
            ownerCode: $demandJointOwner->getOwnerType()?->value,
            roleCode: $demandJointOwner->getRole()?->value,
            isRus: $isRus,
            isCustomer: $isCustomer,
            isDepositor: $isDepositor,
            label: ''
        );

        return $dto;
    }

    public function getJointOwners(array $demandJointOwners)
    {
        $jointOwners = [];

        foreach ($demandJointOwners as $jointOwner) {
            if ($jointOwner->getRole()->value == 1) {
                // TODO: Добавить логику для получения isAllProfileData
                $age = Carbon::parse($jointOwner->getBirthDate())->age;
                $ageCategory = AgeFormatter::getAgeCategory($age);

                $jointOwners[] = new JointOwnerDto(
                    id: $jointOwner->getContactId(),
                    jointOwnerId: $jointOwner->getJointOwnerId(),
                    fullName: $jointOwner->getLastName() . ' ' .
                    $jointOwner->getFirstName() . ' ' .
                    $jointOwner?->getMiddleName(),
                    part: $jointOwner->getPart() ?? '',
                    type: $jointOwner->getOwnerType(),
                    ageCategory: $ageCategory ?? null,
                    isAllProfileData: false,
                    label: $jointOwner->getLabel(),
                    legalEntityData: $jointOwner->getLegalEntityData() ?? null,
                    signatory: $jointOwner->getSignatory() ?? null
                );
            }
        }

        return $jointOwners;
    }
}
