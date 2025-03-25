<?php

namespace App\Services\V2\Sales;

use App\Models\Document\Document;
use App\Models\Document\DocumentType;
use App\Models\DocumentsName;
use App\Models\V2\Sales\Customer\Customer;
use App\Models\User\User;
use App\Models\V2\Contract\Contract;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Models\V2\Sales\Customer\CustomerContractConfidant;
use App\Services\Document\DocumentRepository;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\V2\Contract\ContractRepository;

/**
 * Class DealService
 *
 * @package App\Services\Sales
 */
class ContractService
{
    public function __construct(
        private readonly ContractRepository $contractRepository,
        private DocumentRepository $documentRepository,
        private DynamicsCrmClient $dynamicsCrmClient,
    ) {
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function findContract(string $id, User $user): Contract
    {
        return $this->contractRepository->getByIdV2($id, $user);
    }

    public function getConfidant(Contract $contract, string $jointOwnerId)
    {
        $jointOwners = $contract->getJointOwners();
        $documents = [];
        // $templates = [];

        $neededJointOwner = collect($jointOwners)->filter(function ($jointOwner) use ($jointOwnerId) {
            /** @var CustomerContractConfidant $jointOwner */

            if ($jointOwner->getJointOwnerId() == $jointOwnerId &&
                $jointOwner->getCustomerType()['code'] == 1
            ) {
                return true;
            } elseif ($jointOwner->getJointOwnerId() == $jointOwnerId &&
                $jointOwner->getBirthDate()->age > 14
            ) {
                return true;
            }

            return false;
        })->first();

        // phpcs:disable
        if ($jointOwnerId != null) {
            // $template = $this->documentRepository->getContractDocumentsWithCode(10035, $neededJointOwner->getId(), [40024]) ?? null;
            // if ($template != null) {
            //     $templates[] = reset($template);
            // }

            if ($neededJointOwner != null) {
                $document = $this->documentRepository->getContractDocumentsWithCode(
                    10035,
                    $neededJointOwner->getJointOwnerId(),
                    [2]
                ) ?? null;
            }

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
                    null
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
            // 'templates' => $templates
        ];
    }

    public function getJointOwners(string $id)
    {
        return $this->dynamicsCrmClient->getContractById($id)['jointOwners'];
    }
}
