<?php

namespace App\Services\Sales;

use App\Http\Api\External\V1\Requests\Contract\DemandSummaryRequest;
use App\Http\Api\External\V1\Requests\Sales\SendSmsCodeRequest;
use App\Http\Api\External\V1\Requests\Sales\SetCourierAddressRequest;
use App\Models\Banks;
use App\Models\Contract\ContractDocument;
use App\Models\Document\Document;
use App\Models\Document\DocumentType;
use App\Models\DocumentsName;
use App\Models\Finishing;
use App\Models\Sales\AppealType;
use App\Models\Sales\Customer\Customer;
use App\Models\Sales\Demand\DemandStatus;
use App\Models\User\User;
use App\Models\V2\Contract\Contract;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\Sales\Contract\ContractConfidantService;
use App\Services\Sales\Property\PropertyRepository;
use App\Services\V2\Contract\ContractRepository;
use App\Services\Document\DocumentRepository;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\Sales\Contract\Dto\CreateCourierAddressDto;
use App\Services\Sales\Demand\Dto\SummaryDto;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class DealService
 *
 * @package App\Services\Sales
 */
class ContractService
{
    public function __construct(
        private DynamicsCrmClient        $dynamicsCrmClient,
        private DocumentRepository       $documentRepository,
        private ContractRepository       $contractRepository,
        private PropertyRepository       $propertyRepository,
        private StagesRepository         $stagesRepository,
        private ContractConfidantService $contractLegalEntityService,
    ) {
    }

    /**
     * @throws \App\Services\DynamicsCrm\Exceptions\NotFoundException
     * @throws ValidationException
     * @throws \App\Services\DynamicsCrm\Exceptions\BadRequestException
     */
    public function setCourierAddress(string $contractId, SetCourierAddressRequest $request): void
    {
        $contract = $this->dynamicsCrmClient->getContractById($contractId);
        $isMatch = false;

        foreach ($contract['jointOwners'] as $jointOwner) {
            if ($jointOwner['id'] == $request->get('id')) {
                $isMatch = true;

                $courierAddress = new CreateCourierAddressDto(
                    jointOwnerId: $jointOwner['id'],
                    id: $request->get('id'),
                    city: $request->get('city'),
                    address: $request->get('address'),
                    description: $request->get('description')??null
                );

                $this->dynamicsCrmClient->setCourierAddress($courierAddress);
            }
        }

        if (!$isMatch) {
            throw ValidationException::withMessages([
                'message' => ['Некорректные параметры запроса.'],
            ])->status(429);
        }
    }

    public function sendCode(string $type, string $user_id, SendSmsCodeRequest $request)
    {
        $data = [
            'ObjectId' => $request->contract_id,
            'ObjectTypeCode' => 3,
            'AppealType' => AppealType::from($type)->value . ' - ' . $request->code,
            'AppealText' => $request->code
        ];

        $this->dynamicsCrmClient->sendSmsCode($data, $user_id);
    }

    public function getPayments(string $id)
    {
        return $this->dynamicsCrmClient->getContractById($id);
    }

    public function getContract(string $id)
    {
        return $this->dynamicsCrmClient->getContractById($id);
    }

    public function getPaymentPlan(string $id)
    {
        return $this->dynamicsCrmClient->getContractById($id)['paymentPlan'];
    }

    public function getAllVersionContracts(string $id): array
    {
        $contractDocuments = [];
        $types = [
            '128',
            '1024',
            '2048'
        ];

        $contractDocuments = $this->documentRepository->getContractDocumentsWithTypeCode($id, $types);

        return $contractDocuments;
    }

    public function getAllVersionAdditionalContracts(string $id): array
    {
        $contractDocuments = [];
        $types = [
            '2048',
            '4096',
            '8192',
            '16384',
        ];

        $contractDocuments = $this->documentRepository->getContractDocumentsWithTypeCode($id, $types);

        return $contractDocuments;
    }

    /**
     * @throws \App\Services\DynamicsCrm\Exceptions\NotFoundException
     * @throws \App\Services\DynamicsCrm\Exceptions\BadRequestException
     */
    public function getFinalContracts(string $id): array
    {
        $contractDocuments = [];

        $this->documentRepository->getContractDocumentsWithTypeCode($id, [3]);
        $contractDocuments = $this->documentRepository->getContractDocumentsWithTypeCode($id, [2048]);

        return $contractDocuments;
    }

    public function getGeneralContractDocuments(string $id): array
    {
        $contract = $this->dynamicsCrmClient->getContractById($id);
        $property = $this->dynamicsCrmClient->getPropertyById($contract['articleOrders'][0]['articleId']);
        $types = [];
        $types[] = 64;

        if ($property['isEscrow']) {
            $types[] = 40019;
        }
        if ($contract['paymentModeCode']['code'] == 4 && ($property['electroReg'] ?? null) == true) {
            $types[] = 524494;
        }

        $contractDocuments = $this->documentRepository->getContractDocumentsWithTypeCode($id, $types);

        if ($contractDocuments == []) {
            foreach ($types as $type) {
                $contractDocuments[] = new Document(
                    null,
                    null,
                    null,
                    null,
                    DocumentType::tryFrom(strval($type)),
                    null,
                    null,
                    null,
                    null,
                );
            }
        }

        foreach ($contractDocuments as $key => $contractDocument) {
            $contractDocuments[$key]->setObjectCode($id);
        }

        return $contractDocuments;
    }

    public function getDocuments(string $contractId, string $jointOwnersId, User $user)
    {
        // phpcs:disable
        $contract = $this->dynamicsCrmClient->getContractById($contractId);
        $electroReg = $contract['electroReg'];
        $serviceMainCode = $contract['serviceMain']['code'];
        $jointOwner = collect($contract['jointOwners'])->where('id', '=', $jointOwnersId)->first();
        $templates = [];
        $documents = [];
        if ($contract['jointOwners'][0]['customerType']['code'] == 1) {
            return $this->contractLegalEntityService->getDocument(
                $contractId,
                $contract,
                $electroReg,
                $serviceMainCode,
                $user,
                $jointOwnersId
            );
        }

        if ($electroReg == true) {
            if (($jointOwner['ownerType']['code'] ?? null) == 5) {
                if (Carbon::parse($jointOwner['birthDate'])->age > 18) {
                    if (($jointOwner['familyStatus']['code'] ?? null) == 2) {
                        $template = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32768]) ?? null;
                        if ($template != null) {
                            $templates[0] = reset($template);
                        }

                        if (!in_array($serviceMainCode, ['020020', '020030', '20080'])) {
                            $template = $this->documentRepository->getContractDocumentsWithCode(10035, $jointOwner['id'], [32]) ?? null;
                            if ($template != null) {
                                $templates[1] = reset($template);
                            }
                        }

                        $document = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32770]) ?? null;
                        if ($document != null) {
                            $documents[0] = reset($document);
                        } else {
                            $documents[0] = new Document(
                                null,
                                null,
                                null,
                                null,
                                DocumentType::tryFrom('32770'),
                                null,
                                null,
                                null,
                                null,
                            );
                        }

                        if (!in_array($serviceMainCode, ['020020', '020030', '20080'])) {
                            $document = $this->documentRepository->getContractDocumentsWithCode(10035, $jointOwner['id'], [34]) ?? null;
                            if ($document != null) {
                                $documents[1] = reset($document);
                            } else {
                                $documents[1] = new Document(
                                    null,
                                    null,
                                    null,
                                    null,
                                    DocumentType::tryFrom('34'),
                                    null,
                                    null,
                                    null,
                                    null,
                                );
                            }
                        }

                        $document = $this->documentRepository->getContractDocumentsWithCode(3, $contractId, [40016]) ?? null;
                        if ($document != null) {
                            $documents[2] = reset($document);
                        } else {
                            $documents[2] = new Document(
                                null,
                                null,
                                null,
                                null,
                                DocumentType::tryFrom('40016'),
                                null,
                                null,
                                null,
                                null,
                            );
                        }
                    } elseif (($jointOwner['familyStatus']['code'] ?? null) == 1) {
                        $template = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32768]) ?? null;
                        if ($template != null) {
                            $templates[0] = reset($template);
                        }

                        if (!in_array($serviceMainCode, ['020020', '020030', '20080'])) {
                            $template = $this->documentRepository->getContractDocumentsWithCode(10035, $jointOwner['id'], [36]) ?? null;
                            if ($template != null) {
                                $templates[1] = reset($template);
                            }
                        }

                        $document = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32770]) ?? null;
                        if ($document != null) {
                            $documents[0] = reset($document);
                        } else {
                            $documents[0] = new Document(
                                null,
                                null,
                                null,
                                null,
                                DocumentType::tryFrom('32770'),
                                null,
                                null,
                                null,
                                null,
                            );
                        }

                        if (!in_array($serviceMainCode, ['020020', '020030', '20080'])) {
                            $document = $this->documentRepository->getContractDocumentsWithCode(10035, $jointOwner['id'], [38]) ?? null;
                            if ($document != null) {
                                $documents[1] = reset($document);
                            } else {
                                $documents[1] = new Document(
                                    null,
                                    null,
                                    null,
                                    null,
                                    DocumentType::tryFrom('38'),
                                    null,
                                    null,
                                    null,
                                    null,
                                );
                            }
                        }

                        $document = $this->documentRepository->getContractDocumentsWithCode(10035, $contractId, [40016]) ?? null;
                        if ($document != null) {
                            $documents[2] = reset($document);
                        } else {
                            $documents[2] = new Document(
                                null,
                                null,
                                null,
                                null,
                                DocumentType::tryFrom('40016'),
                                null,
                                null,
                                null,
                                null,
                            );
                        }
                    }
                } elseif (Carbon::parse($jointOwner['birthDate'])->age < 18) {
                    if ($jointOwner['familyStatus']['code'] == 1) {
                        $template = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32768]) ?? null;
                        if ($template != null) {
                            $templates[0] = reset($template);
                        }

                        $document = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32770]) ?? null;
                        if ($document != null) {
                            $documents[0] = reset($document);
                        } else {
                            $documents[0] = new Document(
                                null,
                                null,
                                null,
                                null,
                                DocumentType::tryFrom('32770'),
                                null,
                                null,
                                null,
                                null,
                            );
                        }
                    }
                }
            } elseif (($jointOwner['ownerType']['code'] ?? null) == 4) {
                if (Carbon::parse($jointOwner['birthDate'])->age > 18) {
                    $template = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32768]) ?? null;
                    if ($template != null) {
                        $templates[0] = reset($template);
                    }

                    $document = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32770]) ?? null;
                    if ($document != null) {
                        $documents[0] = reset($document);
                    } else {
                        $documents[0] = new Document(
                            null,
                            null,
                            null,
                            null,
                            DocumentType::tryFrom('32770'),
                            null,
                            null,
                            null,
                            null,
                        );
                    }
                }
            } elseif (($jointOwner['ownerType']['code'] ?? null) == 1) {
                if (Carbon::parse($jointOwner['birthDate'])->age > 18 && $jointOwner['familyStatus']['code'] == 2) {
                    $template = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32768]) ?? null;
                    if ($template != null) {
                        $templates[0] = reset($template);
                    }

                    $document = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32770]) ?? null;
                    if ($document != null) {
                        $documents[0] = reset($document);
                    } else {
                        $documents[0] = new Document(
                            null,
                            null,
                            null,
                            null,
                            DocumentType::tryFrom('32770'),
                            null,
                            null,
                            null,
                            null,
                        );
                    }

                    if (!in_array($serviceMainCode, ['020020', '020030', '20080'])) {
                        $document = $this->documentRepository->getContractDocumentsWithCode(10035, $jointOwner['id'], [40014]) ?? null;
                        if ($document != null) {
                            $documents[1] = reset($document);
                        } else {
                            $documents[1] = new Document(
                                null,
                                null,
                                null,
                                null,
                                DocumentType::tryFrom('40014'),
                                null,
                                null,
                                null,
                                null,
                            );
                        }
                    }

                    $document = $this->documentRepository->getContractDocumentsWithCode(10035, $contractId, [40016]) ?? null;
                    if ($document != null) {
                        $documents[2] = reset($document);
                    } else {
                        $documents[2] = new Document(
                            null,
                            null,
                            null,
                            null,
                            DocumentType::tryFrom('40016'),
                            null,
                            null,
                            null,
                            null,
                        );
                    }
                }
            } elseif (in_array(($jointOwner['ownerType']['code'] ?? null), [2,3])) {
                if (Carbon::parse($jointOwner['birthDate'])->age > 18) {
                    if ($jointOwner['familyStatus']['code'] == 2) {
                        $template = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32768]) ?? null;
                        if ($template != null) {
                            $templates[0] = reset($template);
                        }

                        if (!in_array($serviceMainCode, ['020020', '020030', '20080'])) {
                            $template = $this->documentRepository->getContractDocumentsWithCode(10035, $jointOwner['id'], [32]) ?? null;
                            if ($template != null) {
                                $templates[1] = reset($template);
                            }
                        }

                        $document = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32770]) ?? null;
                        if ($document != null) {
                            $documents[0] = reset($document);
                        } else {
                            $documents[0] = new Document(
                                null,
                                null,
                                null,
                                null,
                                DocumentType::tryFrom('32770'),
                                null,
                                null,
                                null,
                                null,
                            );
                        }

                        if (!in_array($serviceMainCode, ['020020', '020030', '20080'])) {
                            $document = $this->documentRepository->getContractDocumentsWithCode(10035, $jointOwner['id'], [34]) ?? null;
                            if ($document != null) {
                                $documents[1] = reset($document);
                            } else {
                                $documents[1] = new Document(
                                    null,
                                    null,
                                    null,
                                    null,
                                    DocumentType::tryFrom('34'),
                                    null,
                                    null,
                                    null,
                                    null,
                                );
                            }
                        }

                        $document = $this->documentRepository->getContractDocumentsWithCode(10035, $contractId, [40016]) ?? null;
                        if ($document != null) {
                            $documents[2] = reset($document);
                        } else {
                            $documents[2] = new Document(
                                null,
                                null,
                                null,
                                null,
                                DocumentType::tryFrom('40016'),
                                null,
                                null,
                                null,
                                null,
                            );
                        }
                    } elseif ($jointOwner['familyStatus']['code'] == 1) {
                        $template = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32768]) ?? null;
                        if ($template != null) {
                            $templates[0] = reset($template);
                        }

                        if (!in_array($serviceMainCode, ['020020', '020030', '20080'])) {
                            $template = $this->documentRepository->getContractDocumentsWithCode(10035, $jointOwner['id'], [36]) ?? null;
                            if ($template != null) {
                                $templates[1] = reset($template);
                            }
                        }

                        $document = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32770]) ?? null;
                        if ($document != null) {
                            $documents[0] = reset($document);
                        } else {
                            $documents[0] = new Document(
                                null,
                                null,
                                null,
                                null,
                                DocumentType::tryFrom('32770'),
                                null,
                                null,
                                null,
                                null,
                            );
                        }

                        if (!in_array($serviceMainCode, ['020020', '020030', '20080'])) {
                            $document = $this->documentRepository->getContractDocumentsWithCode(10035, $jointOwner['id'], [38]) ?? null;
                            if ($document != null) {
                                $documents[1] = reset($document);
                            } else {
                                $documents[1] = new Document(
                                    null,
                                    null,
                                    null,
                                    null,
                                    DocumentType::tryFrom('38'),
                                    null,
                                    null,
                                    null,
                                    null,
                                );
                            }
                        }

                        $document = $this->documentRepository->getContractDocumentsWithCode(10035, $contractId, [40016]) ?? null;
                        if ($document != null) {
                            $documents[2] = reset($document);
                        } else {
                            $documents[2] = new Document(
                                null,
                                null,
                                null,
                                null,
                                DocumentType::tryFrom('40016'),
                                null,
                                null,
                                null,
                                null,
                            );
                        }
                    }
                } elseif (Carbon::parse($jointOwner['birthDate'])->age < 18) {
                    if ($jointOwner['familyStatus']['code'] == 1) {
                        $template = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32768]) ?? null;
                        if ($template != null) {
                            $templates[0] = reset($template);
                        }

                        $document = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32770]) ?? null;
                        if ($document != null) {
                            $documents[0] = reset($document);
                        } else {
                            $documents[0] = new Document(
                                null,
                                null,
                                null,
                                null,
                                DocumentType::tryFrom('32770'),
                                null,
                                null,
                                null,
                                null,
                            );
                        }
                    }
                }
            }
        } else {
            if (($jointOwner['ownerType']['code'] ?? null) == 5) {
                if (Carbon::parse($jointOwner['birthDate'])->age > 18) {
                    if ($jointOwner['familyStatus']['code'] ?? null == 2) {
                        $template = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32768]) ?? null;
                        if ($template != null) {
                            $templates[0] = reset($template);
                        }

                        if (!in_array($serviceMainCode, ['020020', '020030', '20080'])) {
                            $template = $this->documentRepository->getContractDocumentsWithCode(10035, $jointOwner['id'], [32]) ?? null;
                            if ($template != null) {
                                $templates[1] = reset($template);
                            }
                        }

                        $template = $this->documentRepository->getContractDocumentsWithCode(10035 , $jointOwner['id'], [40017]) ?? null;
                        if ($template != null) {
                            $templates[2] = reset($template);
                        }

                        $template = $this->documentRepository->getContractDocumentsWithCode(10035 , $jointOwner['id'], [40018]) ?? null;
                        if ($template != null) {
                            $templates[3] = reset($template);
                        }

                        $document = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32770]) ?? null;
                        if ($document != null) {
                            $documents[0] = reset($document);
                        } else {
                            $documents[0] = new Document(
                                null,
                                null,
                                null,
                                null,
                                DocumentType::tryFrom('32770'),
                                null,
                                null,
                                null,
                                null,
                            );
                        }

                        $document = $this->documentRepository->getContractDocumentsWithCode(10035, $contractId, [40016]) ?? null;
                        if ($document != null) {
                            $documents[1] = reset($document);
                        } else {
                            $documents[1] = new Document(
                                null,
                                null,
                                null,
                                null,
                                DocumentType::tryFrom('40016'),
                                null,
                                null,
                                null,
                                null,
                            );
                        }
                    } elseif ($jointOwner['familyStatus']['code'] == 1) {
                        $template = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32768]) ?? null;
                        if ($template != null) {
                            $templates[0] = reset($template);
                        }

                        if (!in_array($serviceMainCode, ['020020', '020030', '20080'])) {
                            $template = $this->documentRepository->getContractDocumentsWithCode(10035, $jointOwner['id'], [36]) ?? null;
                            if ($template != null) {
                                $templates[1] = reset($template);
                            }
                        }

                        $template = $this->documentRepository->getContractDocumentsWithCode(10035 , $jointOwner['id'], [40017]) ?? null;
                        if ($template != null) {
                            $templates[2] = reset($template);
                        }

                        $template = $this->documentRepository->getContractDocumentsWithCode(10035 , $jointOwner['id'], [40018]) ?? null;
                        if ($template != null) {
                            $templates[3] = reset($template);
                        }

                        $document = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32770]) ?? null;
                        if ($document != null) {
                            $documents[0] = reset($document);
                        } else {
                            $documents[0] = new Document(
                                null,
                                null,
                                null,
                                null,
                                DocumentType::tryFrom('32770'),
                                null,
                                null,
                                null,
                                null,
                            );
                        }

                        $document = $this->documentRepository->getContractDocumentsWithCode(10035, $contractId, [40016]) ?? null;
                        if ($document != null) {
                            $documents[1] = reset($document);
                        } else {
                            $documents[1] = new Document(
                                null,
                                null,
                                null,
                                null,
                                DocumentType::tryFrom('40016'),
                                null,
                                null,
                                null,
                                null,
                            );
                        }
                    }
                } elseif (Carbon::parse($jointOwner['birthDate'])->age < 18) {
                    if ($jointOwner['familyStatus']['code'] == 1) {
                        $template = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32768]) ?? null;
                        if ($template != null) {
                            $templates[0] = reset($template);
                        }

                        $document = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32770]) ?? null;
                        if ($document != null) {
                            $documents[0] = reset($document);
                        } else {
                            $documents[0] = new Document(
                                null,
                                null,
                                null,
                                null,
                                DocumentType::tryFrom('32770'),
                                null,
                                null,
                                null,
                                null,
                            );
                        }
                    }
                }
            } elseif (($jointOwner['ownerType']['code'] ?? null) == 4) {
                $template = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32768]) ?? null;
                if ($template != null) {
                    $templates[0] = reset($template);
                }

                $document = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32770]) ?? null;
                if ($document != null) {
                    $documents[0] = reset($document);
                } else {
                    $documents[0] = new Document(
                        null,
                        null,
                        null,
                        null,
                        DocumentType::tryFrom('32770'),
                        null,
                        null,
                        null,
                        null,
                    );
                }
            } elseif (($jointOwner['ownerType']['code'] ?? null) == 1) {
                if (Carbon::parse($jointOwner['birthDate'])->age > 18 && $jointOwner['familyStatus']['code'] == 2) {
                    $template = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32768]) ?? null;
                    if ($template != null) {
                        $templates[0] = reset($template);
                    }

                    $template = $this->documentRepository->getContractDocumentsWithCode(10035 , $jointOwner['id'], [40017]) ?? null;
                    if ($template != null) {
                        $templates[1] = reset($template);
                    }

                    $template = $this->documentRepository->getContractDocumentsWithCode(10035 , $jointOwner['id'], [40018]) ?? null;
                    if ($template != null) {
                        $templates[2] = reset($template);
                    }

                    $document = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32770]) ?? null;
                    if ($document != null) {
                        $documents[0] = reset($document);
                    } else {
                        $documents[0] = new Document(
                            null,
                            null,
                            null,
                            null,
                            DocumentType::tryFrom('32770'),
                            null,
                            null,
                            null,
                            null,
                        );
                    }

                    $document = $this->documentRepository->getContractDocumentsWithCode(10035, $contractId, [40016]) ?? null;
                    if ($document != null) {
                        $documents[1] = reset($document);
                    } else {
                        $documents[1] = new Document(
                            null,
                            null,
                            null,
                            null,
                            DocumentType::tryFrom('40016'),
                            null,
                            null,
                            null,
                            null,
                        );
                    }
                }
            } elseif (in_array(($jointOwner['ownerType']['code'] ?? null), [2,3])) {
                if (Carbon::parse($jointOwner['birthDate'])->age > 18) {
                    if ($jointOwner['familyStatus']['code'] == 2) {
                        $template = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32768]) ?? null;
                        if ($template != null) {
                            $templates[0] = reset($template);
                        }

                        if (!in_array($serviceMainCode, ['020020', '020030', '20080'])) {
                            $template = $this->documentRepository->getContractDocumentsWithCode(10035, $jointOwner['id'], [32]) ?? null;
                            if ($template != null) {
                                $templates[1] = reset($template);
                            }
                        }

                        $template = $this->documentRepository->getContractDocumentsWithCode(10035 , $jointOwner['id'], [40017]) ?? null;
                        if ($template != null) {
                            $templates[2] = reset($template);
                        }

                        $template = $this->documentRepository->getContractDocumentsWithCode(10035 , $jointOwner['id'], [40018]) ?? null;
                        if ($template != null) {
                            $templates[3] = reset($template);
                        }

                        $document = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32770]) ?? null;
                        if ($document != null) {
                            $documents[0] = reset($document);
                        } else {
                            $documents[0] = new Document(
                                null,
                                null,
                                null,
                                null,
                                DocumentType::tryFrom('32770'),
                                null,
                                null,
                                null,
                                null,
                            );
                        }

                        $document = $this->documentRepository->getContractDocumentsWithCode(10035, $contractId, [40016]) ?? null;
                        if ($document != null) {
                            $documents[1] = reset($document);
                        } else {
                            $documents[1] = new Document(
                                null,
                                null,
                                null,
                                null,
                                DocumentType::tryFrom('40016'),
                                null,
                                null,
                                null,
                                null,
                            );
                        }
                    } elseif ($jointOwner['familyStatus']['code'] == 1) {
                        $template = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32768]) ?? null;
                        if ($template != null) {
                            $templates[0] = reset($template);
                        }

                        if (!in_array($serviceMainCode, ['020020', '020030', '20080'])) {
                            $template = $this->documentRepository->getContractDocumentsWithCode(10035, $jointOwner['id'], [36]) ?? null;
                            if ($template != null) {
                                $templates[1] = reset($template);
                            }
                        }

                        $template = $this->documentRepository->getContractDocumentsWithCode(10035 , $jointOwner['id'], [40017]) ?? null;
                        if ($template != null) {
                            $templates[2] = reset($template);
                        }

                        $template = $this->documentRepository->getContractDocumentsWithCode(10035 , $jointOwner['id'], [40018]) ?? null;
                        if ($template != null) {
                            $templates[2] = reset($template);
                        }

                        $document = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32770]) ?? null;
                        if ($document != null) {
                            $documents[0] = reset($document);
                        } else {
                            $documents[0] = new Document(
                                null,
                                null,
                                null,
                                null,
                                DocumentType::tryFrom('32770'),
                                null,
                                null,
                                null,
                                null,
                            );
                        }

                        $document = $this->documentRepository->getContractDocumentsWithCode(10035, $contractId, [40016]) ?? null;
                        if ($document != null) {
                            $documents[1] = reset($document);
                        } else {
                            $documents[1] = new Document(
                                null,
                                null,
                                null,
                                null,
                                DocumentType::tryFrom('40016'),
                                null,
                                null,
                                null,
                                null,
                            );
                        }
                    }
                } elseif (Carbon::parse($jointOwner['birthDate'])->age < 18) {
                    if ($jointOwner['familyStatus']['code'] == 1) {
                        $template = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32768]) ?? null;
                        if ($template != null) {
                            $templates[0] = reset($template);
                        }

                        $template = $this->documentRepository->getContractDocumentsWithCode(10035 , $jointOwner['id'], [40017]) ?? null;
                        if ($template != null) {
                            $templates[1] = reset($template);
                        }

                        $template = $this->documentRepository->getContractDocumentsWithCode(10035 , $jointOwner['id'], [40018]) ?? null;
                        if ($template != null) {
                            $templates[2] = reset($template);
                        }

                        $document = $this->documentRepository->getContractDocumentsWithCode(2, $jointOwner['contactId'], [32770]) ?? null;
                        if ($document != null) {
                            $documents[0] = reset($document);
                        } else {
                            $documents[0] = new Document(
                                null,
                                null,
                                null,
                                null,
                                DocumentType::tryFrom('32770'),
                                null,
                                null,
                                null,
                                null,
                            );
                        }
                    }
                }
            }
        }

        if ($documents != []) {
            /** @var Document $document */
            foreach ($documents as $key => $document) {
                if ($document->getObjectCode() == 2 || $document->getType()?->value == 2) {
                    $documents[$key]->setObjectCode($jointOwner['contactId']);
                } elseif ($document->getObjectCode() == 3 || $document->getType()?->value == 3) {
                    $documents[$key]->setObjectCode($contractId);
                } elseif ($document->getObjectCode() == 10035 || $document->getType()?->value == 10035 || $document->getType()?->value == 34) {
                    $documents[$key]->setObjectCode($jointOwner['id']);
                } elseif ($document->getObjectCode() == 38 || $document->getType()?->value == 38) {
                    $documents[$key]->setObjectCode($jointOwner['id']);
                } elseif ($document->getObjectCode() == 40016 || $document->getType()?->value == 40016) {
                    $documents[$key]->setObjectCode($contractId);
                } elseif ($document->getObjectCode() == 32770 || $document->getType()?->value == 32770) {
                    $documents[$key]->setObjectCode($jointOwner['contactId']);
                }
            }
        }

        $contractDocument = new ContractDocument(
            id: $contract['id'],
            jointOwnerId: $jointOwner['id'],
            fullName: ($jointOwner['lastName'] ?? null) . ' ' . ($jointOwner['firstName'] ?? null) . ' ' . ($jointOwner['middleName'] ?? null),
            templates: $templates,
            documents: $documents
        );

        return $contractDocument;
        // phpcs:enable
    }

    public function getJointOwners(string $id)
    {
        return $this->dynamicsCrmClient->getContractById($id)['jointOwners'];
    }

    public function getApprove(array $ids)
    {
        $this->dynamicsCrmClient->getApproveByIds($ids);
    }

    public function getConfidant(Contract $contract, string $jointOwnerId)
    {
        $jointOwners = $contract->getJointOwners();
        $documents = [];
//        $templates = [];

        $neededJointOwner = collect($jointOwners)->filter(function ($jointOwner) use ($jointOwnerId) {
            /** @var Customer $jointOwner */
            if ($jointOwner->getJointOwnerId() == $jointOwnerId && $jointOwner->getBirthDate()->age > 14) {
                return true;
            }

            return false;
        })->first();

        // phpcs:disable
        if ($jointOwnerId != null) {
//            $template = $this->documentRepository->getContractDocumentsWithCode(10035, $neededJointOwner->getId(), [40024]) ?? null;
//            if ($template != null) {
//                $templates[] = reset($template);
//            }

            $document = $this->documentRepository->getContractDocumentsWithCode(10035, $neededJointOwner->getJointOwnerId(), [2]) ?? null;
            if ($document != null) {
                $documents[] = reset($document);
            } else {
                $documents[] = new Document(
                    null,
                    null,
                    null,
                    null,
                    DocumentType::tryFrom('2'),
                    null,
                    null,
                    null,
                    null,
                );
            }

            if ($documents != []) {
                /** @var Document $document */
                foreach ($documents as $key => $document) {
                    $documentName = DocumentsName::where('code', $document->getType()?->value)->first();

                    if ($documentName?->object_type_code == 2) {
                        $documents[$key]->setObjectCode($neededJointOwner->getId());
                    } elseif ($documentName?->object_type_code == 3) {
                        $documents[$key]->setObjectCode($jointOwnerId);
                    } elseif ($documentName?->object_type_code == 10035) {
                        $documents[$key]->setObjectCode($neededJointOwner->getJointOwnerId());
                    }
                }
            }
        }
        // phpcs:enable

        return [
            'user' => $neededJointOwner,
            'documents' => $documents,
//            'templates' => $templates
        ];
    }

    public function getAdditionalContract(string $id)
    {
        $contract = $this->contractRepository->getById($id);
        $orders = [];

        if ($contract->getService()->value == '090010') {
            $property = $this->propertyRepository->getById($contract->getArticleOrders()[0]->getPropertyId());
            $order['space_delta'] = $contract->getArticleOrders()[0]->getQuantity();
            $order['space_bti'] = $property->getSpaceBti();
            $order['space_article'] = $property->getQuantity();
            $order['price'] = $contract->getArticleOrders()[0]->getSum();
            $order['payment_type'] = $contract->getArticleOrders()[0]->getSum() < 0 ? 'return' : 'extra_pay';
            $order['unit_code']['code'] = intval($contract->getArticleOrders()[0]->getUnitCode());
            $order['unit_code']['name'] = $contract->getArticleOrders()[0]->getUnitName();
            $orders[] = $order;
        } elseif ($contract->getService()->value == '090020') {
            $property = $this->propertyRepository->getById($contract->getArticleOrders()[0]->getPropertyId());
            $order['space_delta'] = $contract->getArticleOrders()[0]->getQuantity();
            $order['space_bti'] = $property->getSpaceBti();
            $order['space_article'] = $property->getQuantity() != $property->getSpaceBti() ? $property->getSpaceBti() :
                $property->getSpaceBti() + $contract->getArticleOrders()[0]->getQuantity();
            $order['price'] = $contract->getArticleOrders()[0]->getSum();
            $order['payment_type'] = $contract->getArticleOrders()[0]->getSum() < 0 ? 'return' : 'extra_pay';
            $order['unit_code']['code'] = intval($contract->getArticleOrders()[0]->getUnitCode());
            $order['unit_code']['name'] = $contract->getArticleOrders()[0]->getUnitName();
            $orders[] = $order;
        } elseif ($contract->getService()->value == '030080') {
            $orders = [];
            foreach ($contract->getArticleOrders() as $articleOrder) {
                $order['name'] = $contract->getArticleOrders()[0]->getName();
                $order['cost'] = $contract->getArticleOrders()[0]->getCost();
                $order['sum'] = $contract->getArticleOrders()[0]->getSum();
                $order['quantity'] = $contract->getArticleOrders()[0]->getQuantity();
                $order['unit_code']['code'] = intval($contract->getArticleOrders()[0]->getUnitCode());
                $order['unit_code']['name'] = $contract->getArticleOrders()[0]->getUnitName();
                $orders[] = $order;
            }
        } elseif ($contract->getService()->value == '030090') {
            $order['name'] = $contract->getArticleOrders()[0]->getName();
            $order['cost'] = $contract->getArticleOrders()[0]->getCost();
            $order['sum'] = $contract->getArticleOrders()[0]->getSum();
            $order['quantity'] = $contract->getArticleOrders()[0]->getQuantity();
            $order['unit_code']['code'] = intval($contract->getArticleOrders()[0]->getUnitCode());
            $order['unit_code']['name'] = $contract->getArticleOrders()[0]->getUnitName();
            $orders[] = $order;
        }

        $stages = $this->stagesRepository->MakeAdditionalContractStages($contract);

        $data = [
            'additionalContract' => $contract,
            'orders' => $orders,
            'stages' => $stages
        ];

        return $data;
    }

    public function getAdditionalContracts(string $id, string $type, User $user): array
    {
        $strTypes = '';
        $detailContracts = [];

        if ($type == 'bti') {
            $strTypes = '2050';
        } elseif ($type == 'add_agreement') {
            $strTypes = '65536,100000001,2097152';
        }

        $contracts = $this->contractRepository->getContractsByTypes($strTypes, $user->crm_id);

        foreach ($contracts as $contract) {
            if ($contract->getOpportunityMainId() == $id) {
                $detailContracts[] = $this->contractRepository->getById($contract->getId());
            }
        }

        return $detailContracts;
    }

    public function getArchiveGeneralDocuments(string $id)
    {
        $contract = $this->dynamicsCrmClient->getContractById($id);
        $property = $this->dynamicsCrmClient->getPropertyById($contract['articleOrders'][0]['articleId']);
        $paymentModeCode = $contract['paymentModeCode']['code'];
        $electroReg = $contract['electroReg'];
        $isEscrow = $property['isEscrow'];

        $types = [];
        $types[] = 64;

        if ($isEscrow) {
            $types[] = 40019;
        }
        if ($paymentModeCode == 4 && $electroReg == true) {
            $types[] = 524494;
            $types[] = 524493;
        }

        $contractDocuments = $this->documentRepository->getContractDocumentsWithTypeCode($id, $types);

        return $contractDocuments;
    }

    /**
     * @throws NotFoundException
     * @throws \App\Services\DynamicsCrm\Exceptions\BadRequestException
     */
    public function demandSummary(User $user, DemandSummaryRequest $request)
    {
        $id = $request->get('id');
        $demand = null;
        $demands = [];
        $jointOwners = null;

        if ($request->get('type') == 'contract') {
            // phpcs:disable
            $status = DemandStatus::contract();
            $demand = $this->dynamicsCrmClient->getContractById($id);
        } elseif ($request->get('type') == 'demand') {
            $demand = $this->dynamicsCrmClient->getDemandById($id, $user);
        } else {
            $dto = new SummaryDto(
                paymentMode: null,
                escrowBankName: null,
                letterOfCreditBank: null,
                typeOfOwnership: null,
                jointOwners: null,
                borrowers: null,
                decoration: null,
                deponent: null
            );

            return $dto;
        }

        if ($demand === null) {
            throw new \Exception('Информация по заявке не найдена', 422);
        } else {
            $paymentMode = $demand['paymentModeCode']['name'] ?? null;
            $typeOfOwnership = null;
            $decoration = null;
            $deponent = null;
            $borrowers = null;
            $escrowBankName = null;

            if ($request->get('type') == 'contract') {
                if (($demand['serviceMain']['code'] ?? null) == '020011') {
                    $property = $this->dynamicsCrmClient->getPropertyById($demand['articleOrders'][0]['articleId']);
                    $escrowBankId = $property['escrowBankId'] ?? null;
                    $bank = Banks::where('bank_id', $escrowBankId)?->first();
                    $escrowBankName = $bank?->name;
                }

                $borrowers = null;
            } elseif ($request->get('type') == 'demand') {
                if (($demand['serviceMain']['code'] ?? null) == '020011') {
                    $property = $this->dynamicsCrmClient->getPropertyById($demand['articleId']);
                    $escrowBankId = $property['escrowBankId'] ?? null;
                    $bank = Banks::where('bank_id', $escrowBankId)?->first();
                    $escrowBankName = $bank?->name;
                }

                $borrowers = collect($demand['jointOwners'])->where('roleCode.code', '=', 8)
                    ->map(function ($jointOwner) {
                        $fullName = $jointOwner['lastName'] . ' ' . $jointOwner['firstName'];
                        if (isset($jointOwner['middleName'])) {
                            $fullName .= ' ' . $jointOwner['middleName'];
                        }
                        return $fullName;
                    });
            }

            // phpcs:disable
            if (isset($demand['jointOwners'])) {
                $ownerships = collect($demand['jointOwners'])->where('roleCode.code', '=', 1);
                if ($ownerships != null) {
                    $personalOwnerships = $ownerships?->where('ownerType.code', '=', 5);
                    $sharedOwnerships = $ownerships?->where('ownerType.code', '=', 2);
                    $jointOwnerships = $ownerships?->where('ownerType.code', '=', 1);
                }

                if ($personalOwnerships->count() != 0) {
                    $typeOfOwnership = 'Индивидуальная собственность';
                } elseif ($sharedOwnerships->count() != 0) {
                    $typeOfOwnership = 'Долевая собственность';
                } elseif ($jointOwnerships->count() != 0) {
                    $typeOfOwnership = 'Совместная собственность';
                }

                $jointOwners = collect($demand['jointOwners'])->where('roleCode.code', '=', 1)
                    ->map(function ($jointOwner) {
                        $fullName = $jointOwner['lastName'] . ' ' . $jointOwner['firstName'];
                        if (isset($jointOwner['middleName'])) {
                            $fullName .= ' ' . $jointOwner['middleName'];
                        }
                        if ($jointOwner['customerType'] == "1") { // ЮЛ
                            if (isset($jointOwner['confidant'])) {
                                $fullName .= ' - Юридическое лицо';
                            } else {
                                $fullName .= ' - Индивидуальный предприниматель';
                            }
                        }
                        return $fullName;
                    });

                $depositorFizId = $demand['depositorFizId'] ?? null;

                if ($depositorFizId != null && $request->get('type') == 'contract') {
                    $deponent = collect($demands['jointOwners'])->firstWhere('id', $depositorFizId)
                        ->map(function ($jointOwner) {
                            $fullName = $jointOwner['lastName'] . ' ' . $jointOwner['firstName'];
                            if ($jointOwner['middleName']) {
                                $fullName .= ' ' . $jointOwner['middleName'];
                            }
                            return $fullName;
                        });
                } else {
                    if ($request->get('type') == 'demand' || $request->get('type') == 'contract') {
                        $deponent = $this->depositorUrId($demand);
                    }
                }
            }

            $letterOfCreditBank = Banks::where('bank_id', ($demand['letterOfCreditBankId'] ?? null))->first()?->name;

            $baseFinishVariant = $demand['baseFinishVariant'] ?? null;
            if ($baseFinishVariant != null) {
                $decoration = Finishing::where('finishing_id', '=', $baseFinishVariant['id'])->first()?->name;
            }

            $dto = new SummaryDto(
                paymentMode: $paymentMode,
                escrowBankName: $escrowBankName,
                letterOfCreditBank: $letterOfCreditBank,
                typeOfOwnership: $typeOfOwnership,
                jointOwners: $jointOwners?->toArray(),
                borrowers: $borrowers?->toArray(),
                decoration: $decoration ?? ($demand['baseFinishVariant']['name'] ?? null),
                deponent: $deponent
            );

            return $dto;
        }
        // phpcs:enable
    }

    private function depositorUrId($demand)
    {
        $depositorUrId = $demand['depositorUrId'] ?? null;
        if ($depositorUrId != null) {
            return collect($demand['jointOwners'])->firstWhere('id', $depositorUrId)['firstName'] ?? null;
        } else {
            return null;
        }
    }
    public function findContract($id)
    {
        return $this->contractRepository->getById($id);
    }

    public function getUkDocuments($id, User $user)
    {
        $contracts = $this->contractRepository->getContractsByTypes('129', $user->crm_id);
        $mainContract = null;

        foreach ($contracts as $contract) {
            if ($contract->getOpportunityMainId() == $id) {
                $mainContract = $contract;
                break;
            }
        }

        if ($mainContract != null) {
            $types = [
                '40020',
                '40021',
                '40022',
                '40023'
            ];

            // phpcs:disable
            $contractDocuments = $this->documentRepository->getContractDocumentsWithTypeCode($mainContract->getId(), $types);
            // phpcs:enable

            if ($contractDocuments != null) {
                return $contractDocuments;
            } else {
                return [];
            }
        } else {
            return [];
        }
    }
}
