<?php

namespace App\Services\Sales\JointOwner;

use App\Http\Api\External\V1\Requests\Deal\StoreJointOwnerRequest;
use App\Http\Api\External\V1\Requests\Deal\UpdateJointOwnerRequest;
use App\Http\Api\External\V1\Requests\Request;
use App\Models\Gender;
use App\Models\Role;
use App\Models\Sales\Customer\Customer;
use App\Models\Sales\Demand\Demand;
use App\Models\Sales\JointOwner\JointOwner;
use App\Models\Sales\OwnerType;
use App\Models\User\User;
use App\Services\Document\DocumentRepository;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\Sales\Customer\CustomerRepository;
use App\Services\Sales\JointOwner\Dto\CreateJointOwnerLeadDto;
use App\Services\Sales\JointOwner\Dto\GetJointOwnerDto;
use App\Services\Sales\JointOwner\Dto\JointOwnerDto;
use App\Services\Sales\JointOwner\Dto\StoreParticipantDto;
use App\Services\Sales\JointOwner\Dto\UpdateJointOwnerDto;
use App\Services\Sales\JointOwner\Exceptions\EmailExistException;
use App\Services\Sales\JointOwner\Exceptions\PhoneExistException;
use Carbon\Carbon;

/**
 * Class DemandService
 *
 * @package App\Services\Sales\Demand
 */
class JointOwnerService
{
    public function __construct(
        private DynamicsCrmClient $dynamicsCrmClient,
        private DocumentRepository $documentRepository,
        private CustomerRepository $customerRepository,
    ) {
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function deleteJointOwner(string $demandId, string $jointOwnerId)
    {
        try {
            $this->dynamicsCrmClient->deleteJointOwner($demandId, $jointOwnerId);
        } catch (\Throwable $throwable) {
            $this->dynamicsCrmClient->deleteDemandJointOwner($demandId, $jointOwnerId);
        }
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function emailCheck(string $email)
    {
        return $this->dynamicsCrmClient->emailCheck($email);
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function setJointOwnerCustomer(string $demandId, string $jointOwnerId)
    {
        return $this->dynamicsCrmClient->setJointOwnerCustomer($demandId, $jointOwnerId);
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws PhoneExistException
     */
    public function updateJointOwner(string $demandId, string $jointOwnerId, UpdateJointOwnerRequest $request)
    {
        $jointOwnerLeadDto = new UpdateJointOwnerDto(
            ownerType: OwnerType::tryFrom(strval($request->owner_code)),
            lastName: $request->last_name,
            firstName: $request->first_name,
            middleName: $request->middle_name,
            gender: $request->gender,
            phone: $request->phone,
            email: $request->email,
            birthDate: $request->birth_date,
            inn: $request->inn,
            snils: $request->snils,
            role: Role::tryFrom(strval($request->role_code)),
            married: $request->married,
            isRus: $request->is_rus,
            part: $request->part,
        );

        try {
            return $this->dynamicsCrmClient->updateJointOwnerLead($demandId, $jointOwnerId, $jointOwnerLeadDto);
        } catch (PhoneExistException $e) {
            throw new PhoneExistException();
        }
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getParticipant($ownerCode, $jointOwnerId, $demandid, User $user)
    {
        $jointOwners = JointOwner::byUserId($user->id)->get();
        $ownerType = OwnerType::from(strval($ownerCode));

        $dto = new StoreParticipantDto(
            ownerType: $ownerType,
            jointownerId: $jointOwnerId,
            demandid: $demandid,
        );

        $jointOwnersFromCrm = $this->dynamicsCrmClient->getDemandById($dto->demandid, $user)['jointOwners'];
        $jointOwnersIds = collect($jointOwnersFromCrm)->pluck('contactId')->toArray();

        $filteredJointOwners = $jointOwners->filter(function ($jointOwner) use ($jointOwnersIds, $ownerCode, $user) {
            $userFromDb = $jointOwner->user()?->first();
            $inArr = in_array($userFromDb?->crm_id, $jointOwnersIds);
            if ($inArr) {
                if ($ownerCode == 1) {
                    return $jointOwner->gender != 'male';
                } elseif ($ownerCode == 2) {
                    return true;
                } elseif ($ownerCode == 3) {
                    return $jointOwner->gender != 'male';
                } elseif ($ownerCode == 4) {
                    return true;
                } elseif ($ownerCode == 5) {
                    return Carbon::parse($jointOwner->birth_date)->year < 18;
                }
            } else {
                return false;
            }
        });

        if ($filteredJointOwners->count() != 0) {
            $filteredJointOwners = $filteredJointOwners->toArray();
        } else {
            $filteredJointOwners = [];
        }

        return $filteredJointOwners;
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws EmailExistException
     * @throws PhoneExistException
     */
    public function setJointOwnerLead(Demand $demand, StoreJointOwnerRequest $request, User $user)
    {
        $demandId = $demand->getId();

        $jointOwners = collect($demand->getJointOwners())->filter(function ($jointOwner) {
            /** @var Customer $jointOwner */
            return $jointOwner->getRole()->value === '1';
        });

        // OwnerTypeCode == 5
        $jointOwnersWOC5 = $jointOwners->filter(function ($jointOwner) {
            /** @var Customer $jointOwner */
            return $jointOwner->getOwnerType()?->value === '5';
        });

        // OwnerTypeCode == 2
        $jointOwnersWOC2 = $jointOwners->filter(function ($jointOwner) {
            /** @var Customer $jointOwner */
            return $jointOwner->getOwnerType()?->value === '2';
        });

        // OwnerTypeCode == 1
        $jointOwnersWOC1 = $jointOwners->filter(function ($jointOwner) {
            /** @var Customer $jointOwner */
            return $jointOwner->getOwnerType()?->value === '1';
        });

        // phpcs:disable
        /** @var Customer[] $jointOwners */
        if ($request->get('owner_code') == 5 && $jointOwnersWOC5->count() == 1 &&
            ($jointOwnersWOC5->first()->getOwnerType()?->value == '-1' || $jointOwnersWOC5->first()->getOwnerType() == null)) {
            $body = [
                'JointOwner' => [
                    'OwnerType' => [
                        'code' => 4
                    ]
                ]
            ];

            $this->dynamicsCrmClient->setJointOwnerWithBody($demandId, $jointOwnersWOC5->first()->getId(), $body);
        } elseif ($request->get('owner_code') == 2 && $jointOwnersWOC2->count() == 1 &&
            ($jointOwnersWOC2->first()->getOwnerType()?->value == '-1' || $jointOwnersWOC2->first()->getOwnerType()?->value == null)) {
            $body = [
                'JointOwner' => [
                    'OwnerType' => [
                        'code' => 4
                    ]
                ]
            ];

            $this->dynamicsCrmClient->setJointOwnerWithBody($demandId, $jointOwnersWOC2->first()->getId(), $body);
        } elseif ($request->get('owner_code') == 1 && $jointOwnersWOC1->count() == 1 &&
            ($jointOwnersWOC1->first()->getOwnerType()?->value == '-1' || $jointOwnersWOC1->first()->getOwnerType() == null)) {
            $body = [
                'JointOwner' => [
                    'OwnerType' => [
                        'code' => 1
                    ],
                    'familyStatus' => [
                        'code' => 2
                    ]
                ]
            ];

            $this->dynamicsCrmClient->setJointOwnerWithBody($demandId, $jointOwnersWOC1->first()->getId(), $body);
        } elseif ($request->get('owner_code') == 2 && $jointOwnersWOC2->count() > 1) {
            $filteredJointOwners = collect($jointOwners)->filter(function ($jointOwner) {
                /** @var Customer $jointOwner */
                return $jointOwner->getOwnerType()->value === 2;
            });

            $countOfJointOwners = $jointOwnersWOC2->count() + 1;

            foreach ($filteredJointOwners as $filteredJointOwner) {
                $body = [
                    'JointOwner' => [
                        'part' => "1/$countOfJointOwners"
                    ]
                ];

                $this->dynamicsCrmClient->setJointOwnerWithBody($demandId, $filteredJointOwner->getId(), $body);
            }
        }
        // phpcs:enable

        $jointOwnerLeadDto = new CreateJointOwnerLeadDto(
            ownerType: OwnerType::from(strval($request->owner_code)??null),
            lastName: $request->last_name,
            firstName: $request->first_name,
            middleName: $request->middle_name,
            gender: $request->gender,
            phone: $request->phone,
            email: $request->email,
            birthDate: $request->birth_date,
            inn: $request->inn,
            snils: $request->snils,
            role: Role::tryFrom(strval($request->role_code??null)),
            married: $request->married,
            isRus: $request->is_rus,
            part: $request->part??'',
            files: $request->get('files')
        );

        try {
            $customer = $this->dynamicsCrmClient->setJointOwnerLead($demandId, $jointOwnerLeadDto);
        } catch (PhoneExistException $e) {
            throw new PhoneExistException();
        }

        if ($jointOwnerLeadDto->isRus) {
            if ((Carbon::now()->year - Carbon::parse($jointOwnerLeadDto->birthDate)->year) >= 14) {
                $code = 1;
            } else {
                $code = 2;
            };
        } else {
            $code = 4;
        }

        $code = [
            'DocumentType' => [
                'code' => $code
            ]
        ];

        $this->dynamicsCrmClient->putCustomer($customer['contactId'], $code);
        $contactId = $customer['contactId'];
        $jointOwnerId = $customer['jointOwnerId'];
        $customerFromCrm = $this->dynamicsCrmClient->getCustomerById($customer['contactId']);

        JointOwner::create([
            'user_id' => $user->id,
            'first_name' => $customerFromCrm['firstName'],
            'last_name' => $customerFromCrm['lastName'],
            'middle_name' => $customerFromCrm['middleName'] ?? null,
            'gender' => 'male',
            'birth_date' => $customerFromCrm['birthDate'],
            'crm_id' => $customerFromCrm['id']
        ]);

        if ($request->get('files') != null) {
            foreach ($request->files as $file) {
                $annotations[] = [
                    'Name' => $file['name'],
                    'FileName' => $file['name'],
                    'DocumentType' => [
                        'code' => $file['document_type'],
                    ],
                    'IsCustomerAvailable' => true,
                    'DocumentBody' => $file['body'],
                    'MimeType' => $file['mime_type'],
                ];
            }

            $body = [
                'ObjectId' => $request->object_id,
                'ObjectTypeCode' => $request->object_type,
                'Annotations' => $annotations
            ];

            $this->dynamicsCrmClient->uploadSaleFile($body, $contactId);
        }

        return ['contactId' => $contactId, 'jointOwnerId' => $jointOwnerId];
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

    public function getJointOwners(Demand $demand)
    {
        // phpcs:disable
        $jointOwners = [];

        foreach ($demand->getJointOwners() as $jointOwner) {
            if ($jointOwner->getRole()->value == 1) {
                //TODO
                //Добавить логику для получения isAllProfileData
                $age = Carbon::parse($jointOwner->getBirthDate())->age;
                $ageCategory = '';

                if ($age > 18) {
                    $ageCategory = 'adult';
                } elseif ($age > 14 && $age < 18) {
                    $ageCategory = 'teen';
                } elseif ($age < 14) {
                    $ageCategory = 'child';
                }

                $jointOwners[] = new JointOwnerDto(
                    id: $jointOwner->getContactId(),
                    jointOwnerId: $jointOwner->getId(),
                    fullName: $jointOwner->getLastName() . ' ' . $jointOwner->getFirstName() . ' ' . $jointOwner?->getMiddleName(),
                    part: $jointOwner->getPart() ?? '',
                    type: $jointOwner->getOwnerType(),
                    ageCategory: $ageCategory,
                    isAllProfileData: false,
                    label: $jointOwner->getLabel(),
                );
            }
        }

        return $jointOwners;
    }
}
