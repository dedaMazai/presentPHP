<?php

namespace App\Services\Sales\Contract;

use App\Models\Contract\ContractDocument;
use App\Models\Document\Document;
use App\Models\Document\DocumentType;
use App\Services\Document\DocumentRepository;
use App\Services\DynamicsCrm\DynamicsCrmClient;

class ContractConfidantService
{

    public function __construct(
        private DocumentRepository $documentRepository,
        private DynamicsCrmClient  $dynamicsCrmClient,
    ) {
    }
    public function getDocument(
        $contractId,
        $contract,
        $electroReg,
        $serviceMainCode,
        $user,
        $jointOwnersId
    ) {
        $jointOwner = $contract['jointOwners'][0];

        // здесь просто isset
        if (isset($jointOwner['confidant'])) {
            $templates = [];
            $documents = $this->confidantValidatedDocumentExists($contractId, $jointOwner);

            $contractDocument = new ContractDocument(
                id: $contract['id'],
                jointOwnerId: $jointOwner['id'],
                fullName: $jointOwner['name'] ?? null,
                templates: $templates,
                documents: $documents
            );

            return $contractDocument;
        } else {
            $confidant = $this->dynamicsCrmClient->getCustomerById($user->crm_id);
            return $this->confidantValidatedDocumentNotExists(
                $electroReg,
                $confidant,
                $serviceMainCode,
                $contractId,
                $contract,
                $jointOwner,
                $jointOwnersId
            );
        }
    }

    public function confidantValidatedDocumentExists($contractId, $jointOwner)
    {
        $document = $this->documentRepository->getContractDocumentsWithCode(3, $contractId, [40010]) ?? null;
        if ($document != null) {
            $documents[0] = reset($document);
        } else {
            $documents[0] = new Document(
                null,
                null,
                null,
                null,
                DocumentType::tryFrom('40010'),
                null,
                null,
                null,
                null,
            );
        }

        $document = $this->documentRepository->getContractDocumentsWithCode(10035, $jointOwner['id'], [40016]) ?? null;
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

        return $documents;
    }

    private function confidantValidatedDocumentNotExists(
        $electroReg,
        $confidant,
        $serviceMainCode,
        $contractId,
        $contract,
        $jointOwner,
        $jointOwnersId
    ) {
        if ($electroReg == true) {
            return $this->electroRegTrueValidation(
                $confidant,
                $serviceMainCode,
                $contractId,
                $contract,
                $jointOwner,
                $jointOwnersId
            );
        } else {
            return $this->electroRegFalseValidation(
                $confidant,
                $serviceMainCode,
                $contractId,
                $contract,
                $jointOwner,
                $jointOwnersId
            );
        }
    }

    private function electroRegTrueValidation(
        $confidant,
        $serviceMainCode,
        $contractId,
        $contract,
        $jointOwner,
        $jointOwnersId
    ) {
        if (($confidant['familyStatus']['code'] ?? null) == 2) {
            return $this->familyStatusValidationTrue(
                $serviceMainCode,
                $contractId,
                $contract,
                $jointOwner,
                $jointOwnersId
            );
        } else {
            return $this->familyStatusValidationFalse(
                $serviceMainCode,
                $contractId,
                $contract,
                $jointOwner,
                $jointOwnersId
            );
        }
    }

    private function electroRegFalseValidation(
        $confidant,
        $serviceMainCode,
        $contractId,
        $contract,
        $jointOwner,
        $jointOwnersId
    ) {
        if (($confidant['familyStatus']['code'] ?? null) == 2) {
            return $this->familyStatusValidationTrueV2(
                $serviceMainCode,
                $contractId,
                $contract,
                $jointOwner,
                $jointOwnersId
            );
        } else {
            return $this->familyStatusValidationFalseV2(
                $serviceMainCode,
                $contractId,
                $contract,
                $jointOwner,
                $jointOwnersId
            );
        }
    }

    private function familyStatusValidationTrue(
        $serviceMainCode,
        $contractId,
        $contract,
        $jointOwner,
        $jointOwnersId
    ) {
        $templates = [];
        $documents = [];
        $template = $this
            ->documentRepository
            ->getContractDocumentsWithCode(2, $jointOwner['id'], [32768]) ?? null;
        if ($template != null) {
            $templates[0] = reset($template);
        }

        if (!in_array($serviceMainCode, ['020020', '020030', '20080'])) {
            $template = $this->documentRepository->getContractDocumentsWithCode(10035, $jointOwnersId, [32]) ?? null;
            if ($template != null) {
                $templates[1] = reset($template);
            }
        }

        $document = $this->
        documentRepository->
        getContractDocumentsWithCode(2, $jointOwner['id'], [32770]) ?? null;
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

        $document = $this->
        documentRepository->
        getContractDocumentsWithCode(10035, $jointOwnersId, [34]) ?? null;
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

        $document = $this->
        documentRepository->
        getContractDocumentsWithCode(3, $contractId, [40016]) ?? null;
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
        return $this->successful($contract, $jointOwner, $templates, $documents);
    }

    private function familyStatusValidationTrueV2(
        $serviceMainCode,
        $contractId,
        $contract,
        $jointOwner,
        $jointOwnersId
    ) {
        $templates = [];
        $template = $this->
        documentRepository->
        getContractDocumentsWithCode(2, $jointOwner['id'], [32768]) ?? null;
        if ($template != null) {
            $templates[0] = reset($template);
        }

        if (!in_array($serviceMainCode, ['020020', '020030', '20080'])) {
            $template = $this->
            documentRepository->
            getContractDocumentsWithCode(10035, $jointOwnersId, [32]) ?? null;
            if ($template != null) {
                $templates[1] = reset($template);
            }
        }

        $template = $this->
        documentRepository->
        getContractDocumentsWithCode(10035, $jointOwnersId, [40017]) ?? null;
        if ($template != null) {
            $templates[2] = reset($template);
        }

        $template = $this->
        documentRepository->
        getContractDocumentsWithCode(10035, $jointOwnersId, [40018]) ?? null;
        if ($template != null) {
            $templates[3] = reset($templates);
        }

        $document = $this->
        documentRepository->
        getContractDocumentsWithCode(2, $jointOwner['id'], [32770]) ?? null;
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

        $document = $this->
        documentRepository->
        getContractDocumentsWithCode(10035, $contractId, [40016]) ?? null;
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
        return $this->successful($contract, $jointOwner, $templates, $documents);
    }

    private function familyStatusValidationFalse(
        $serviceMainCode,
        $contractId,
        $contract,
        $jointOwner,
        $jointOwnersId
    ) {
        $templates = [];
        $documents = [];
        $template = $this->
        documentRepository->
        getContractDocumentsWithCode(2, $jointOwner['id'], [32768]) ?? null;
        if ($template != null) {
            $templates[0] = reset($template);
        }

        if (!in_array($serviceMainCode, ['020020', '020030', '20080'])) {
            $template = $this->
            documentRepository->
            getContractDocumentsWithCode(10035, $jointOwnersId, [36]) ?? null;
            if ($template != null) {
                $templates[1] = reset($template);
            }
        }


        $document = $this->
        documentRepository->
        getContractDocumentsWithCode(2, $jointOwner['id'], [32770]) ?? null;
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
            $document = $this->
            documentRepository->
            getContractDocumentsWithCode(10035, $jointOwnersId, [38]) ?? null;
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

        $document = $this->
        documentRepository->
        getContractDocumentsWithCode(10035, $contractId, [40016]) ?? null;
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
        return $this->successful($contract, $jointOwner, $templates, $documents);
    }

    private function familyStatusValidationFalseV2(
        $serviceMainCode,
        $contractId,
        $contract,
        $jointOwner,
        $jointOwnersId
    ) {
        $templates = [];

        $template = $this->
        documentRepository->
        getContractDocumentsWithCode(2, $jointOwner['id'], [32768]) ?? null;
        if ($template != null) {
            $templates[0] = reset($template);
        }

        if (!in_array($serviceMainCode, ['020020', '020030', '20080'])) {
            $template = $this->
            documentRepository->
            getContractDocumentsWithCode(10035, $jointOwnersId, [36]) ?? null;
            if ($template != null) {
                $templates[1] = reset($template);
            }
        }

        $template = $this->
        documentRepository->
        getContractDocumentsWithCode(1035, $jointOwnersId, [40017]) ?? null;
        if ($template != null) {
            $templates[2] = reset($template);
        }

        $template = $this->
        documentRepository->
        getContractDocumentsWithCode(1035, $jointOwnersId, [40018]) ?? null;
        if ($template != null) {
            $templates[3] = reset($template);
        }

        $document = $this->
        documentRepository->
        getContractDocumentsWithCode(2, $jointOwner['id'], [32770]) ?? null;
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

        $document = $this->
        documentRepository->
        getContractDocumentsWithCode(10035, $contractId, [40016]) ?? null;
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
        return $this->successful($contract, $jointOwner, $templates, $documents);
    }

    private function successful($contract, $jointOwner, $templates, $documents)
    {
        $fullName = trim(
            ($jointOwner['lastName'] ?? '') . ' ' .
            ($jointOwner['firstName'] ?? '') . ' ' .
            ($jointOwner['middleName'] ?? '')
        );

        // Если fullName пустое, используем name
        if (empty($fullName)) {
            $fullName = $jointOwner['name'] ?? null;
        }

        $contractDocument = new ContractDocument(
            id: $contract['id'],
            jointOwnerId: $jointOwner['id'],
            fullName: $fullName,
            templates: $templates,
            documents: $documents
        );

        return $contractDocument;
    }
}
