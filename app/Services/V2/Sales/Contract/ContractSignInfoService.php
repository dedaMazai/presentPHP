<?php

namespace App\Services\V2\Sales\Contract;

use App\Models\SignDocuments;
use App\Services\Contract\Dto\ContractJointOwnerDto;
use App\Services\Document\DocumentRepository;
use App\Services\Sales\Customer\CustomerRepository;
use App\Services\V2\Contract\ContractRepository;
use Carbon\Carbon;

class ContractSignInfoService
{

    public function __construct(
        private CustomerRepository $customerRepository,
        private DocumentRepository $documentRepository,
    ) {
    }
    public function getJointOwnersSignInfo(
        array $jointOwners,
    ) {
        $adultsJointOwners = [];
        $contractJointOwners = [];
        $isCommonInfoSignMeAvailable = false;
        foreach ($jointOwners as $jointOwner) {
            if (isset($jointOwner['birthDate']) && Carbon::parse($jointOwner['birthDate'])->age >= 18
                || $jointOwner['customerType']['code'] == 1) {
                $documents = [];
                $document = null;
                if ($jointOwner['customerType']['code'] == 1) {
                    $customer = $this->customerRepository->getById($jointOwner['primaryContact']['code']);
                } else {
                    $customer = $this->customerRepository->getById($jointOwner['contactId']);
                }

                if (isset($jointOwner['contactId'])) {
                    $documentList = $this->documentRepository->getDocumentsByCrmIdWithTypeCode(
                        $jointOwner['contactId'],
                        [65538]
                    );

                    foreach ($documentList as $document) {
                        if ($document->getType()->value == '65538') {
                            $documents[] = $document;
                        }
                    }

                    if (count($documents) > 1) {
                        usort($documents, 'compareCreatedOn');

                        $document = $documents[0];
                    } elseif (count($documents) == 1) {
                        $document = $documents[0];
                    } else {
                        $document = null;
                    }
                } elseif (isset($jointOwner['primaryContact']['code'])) {
                    $documentList = $this->documentRepository->getDocumentsByCrmIdWithTypeCode(
                        $jointOwner['primaryContact']['code'],
                        [65538]
                    );

                    foreach ($documentList as $document) {
                        if ($document->getType()->value == '65538') {
                            $documents[] = $document;
                        }
                    }

                    if (count($documents) > 1) {
                        usort($documents, 'compareCreatedOn');

                        $document = $documents[0];
                    } elseif (count($documents) == 1) {
                        $document = $documents[0];
                    } else {
                        $document = null;
                    }
                }

                if ($isCommonInfoSignMeAvailable == false) {
                    $isCommonInfoSignMeAvailable = $customer->getSignStatus()?->value == 43;
                }

                $jointOwner['customer'] = $customer;
                $jointOwner['document'] = $document;
                $contractJointOwners[] = $jointOwner;
            }
        }

        $reissueGuideUrl = SignDocuments::where('code', '=', 'reissue_guide')->first()?->document_id;
        // phpcs:disable
        $courierSignInfo = 'Для выпуска эл. подписи необходимо заказать выезд курьера. Курьер привезет заявление для выпуска на подпись и сверит информацию, указанную в заявлении, с оригиналом вашего паспорта. После проверки курьер сделает фото будущего владельца эл. подписи с заявлением';
        $dto = new ContractJointOwnerDto(
            courierSignInfo: $courierSignInfo,
            isCommonInfoSignMeAvailable: $isCommonInfoSignMeAvailable,
            reissueGuideUrl: $reissueGuideUrl,
            jointOwners: $contractJointOwners
        );
        return $dto;
    }
}
