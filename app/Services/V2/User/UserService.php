<?php

namespace App\Services\V2\User;

use App\Models\Document\Document;
use App\Models\Document\DocumentType;
use App\Models\User\User;
use App\Services\Customer\CustomerRepository;
use App\Services\Document\DocumentRepository;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use Carbon\Carbon;

/**
 * Class UserService
 *
 * @package App\Services\V2\User
 */
class UserService
{
    public function __construct(
        private readonly DocumentRepository $userDocumentRepository,
        private readonly CustomerRepository $customerRepository,
    ) {
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getDocuments(User $user): array
    {
        $customer = $this->customerRepository->getById($user->crm_id);
        $customerDocType = $customer?->getDocumentType()?->value;
        $documents = [];
        $codes = [];

        // phpcs:disable
        if (($customerDocType === 1 || $customerDocType === null) &&
            Carbon::parse($customer->getBirthDate())->age > 18) {
            $codes = ['524491', '524492', '4'];
            $documents = $this->userDocumentRepository->getDocumentsByUserWithTypeCode($user, $codes);
        } elseif (($customerDocType !== 1 && $customerDocType !== null) &&
            Carbon::parse($customer->getBirthDate())->age > 18) {
            $codes = ['40001', '40002', '4'];
            $documents = $this->userDocumentRepository->getDocumentsByUserWithTypeCode($user, $codes);
        } elseif (($customerDocType === 1 || $customerDocType === null) &&
            Carbon::parse($customer->getBirthDate())->age < 18 && Carbon::parse($customer->getBirthDate())->age > 14) {
            $codes = ['524491', '524492 ', '40003', '4'];
            $documents = $this->userDocumentRepository->getDocumentsByUserWithTypeCode($user, $codes);
        } elseif (($customerDocType !== 1 && $customerDocType !== null) &&
            Carbon::parse($customer->getBirthDate())->age < 18 && Carbon::parse($customer->getBirthDate())->age > 14) {
            $codes = ['40001', '40002', '40004'];
            $documents = $this->userDocumentRepository->getDocumentsByUserWithTypeCode($user, $codes);
        } elseif (($customerDocType === 1 || $customerDocType === null) &&
            Carbon::parse($customer->getBirthDate())->age < 14) {
            $codes = ['40003'];
            $documents = $this->userDocumentRepository->getDocumentsByUserWithTypeCode($user, $codes);
        } elseif (($customerDocType !== 1 && $customerDocType !== null) &&
            Carbon::parse($customer->getBirthDate())->age < 14) {
            $codes = ['40002', '40004'];
            $documents = $this->userDocumentRepository->getDocumentsByUserWithTypeCode($user, $codes);
        }
        // phpcs:enable

        if ($codes !== []) {
            $filteredCodes = array_filter($codes, static function ($code) use ($documents) {
                foreach ($documents as $document) {
                    /** @var Document $document */
                    if ($document->getType()->value === $code) {
                        return false;
                    }
                }
                return true;
            });

            foreach ($filteredCodes as $code) {
                $documents[] = new Document(
                    null,
                    null,
                    null,
                    null,
                    DocumentType::tryFrom((string)$code),
                    null,
                    null,
                    null,
                    null
                );
            }
        }

        return $documents;
    }
}
