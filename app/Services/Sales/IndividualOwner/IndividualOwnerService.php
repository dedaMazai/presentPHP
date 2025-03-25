<?php

namespace App\Services\Sales\IndividualOwner;

use App\Models\Sales\Gender;
use App\Services\Document\DocumentRepository;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\Sales\IndividualOwner\Dto\IndividualOwnerDto;
use App\Services\Utils\AgeFormatter;
use App\Services\Utils\DateFormatter;
use App\Services\Utils\DocumentTypeCodeFormatter;
use Carbon\Carbon;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class IndividualOwnerService
{
    public function __construct(
        protected DynamicsCrmClient $dynamicsCrmClient,
        protected DocumentRepository $documentRepository
    ) {
    }

    public function findIndividualOwnerInfo($user)
    {
        try {
            $customer = $this->dynamicsCrmClient->getCustomerById($user->crm_id);
            return $this->makeIndividualOwner($customer, $user);
        } catch (Exception $e) {
            throw new NotFoundHttpException('Individual owner info not found');
        }
    }

    private function makeIndividualOwner($data, $user)
    {
        $age = DateFormatter::birthDateFormatter($data['birthDate']);
        $documentTypeCode = $data['documentType']['code'];
        $ageCategory = AgeFormatter::getAgeCategory($age);
        $documentTypeCodes = DocumentTypeCodeFormatter::documentTypeCode($age, $documentTypeCode);

        $isRus = AgeFormatter::isRus($ageCategory, $documentTypeCode);
        $documents = $this->documentRepository->getDocumentsByUserWithTypeCode($user, $documentTypeCodes);

        return new IndividualOwnerDto(
            lastName: $data['lastName'] ?? null,
            firstName: $data['firstName'] ?? null,
            middleName: $data['middleName'] ?? null,
            gender: Gender::tryFrom($data['genderCode']['code'] ?? ''),
            phone: $data['phone'] ?? null,
            phoneHome: $data['phoneHome'] ?? null,
            email: $data['email'] ?? null,
            birthDate: isset($data['birthDate'])?
                new Carbon($data['birthDate']):
                Carbon::parse('00:00:00 01.01.1970'),
            inn: $data['inn'] ?? null,
            snils: $data['snils'] ?? null,
            married: $data['familyStatus']['code'] ?? null,
            is_rus: $isRus,
            documents: $documents
        );
    }

    public function updateIndividualOwnerInfo($user, $data)
    {
        $apiData = $this->transformData($data);
        try {
            return $this->dynamicsCrmClient->putCustomer($user->crm_id, $apiData);
        } catch (Exception $e) {
            throw new NotFoundHttpException($e->getMessage());
        }
    }

    private function transformData(array $data): array
    {
        $transformed = [
            'firstName'    => $data['first_name'],
            'lastName'     => $data['last_name'],
            'middleName'   => $data['middle_name'] ?? null,
            'phone'        => $data['phone'] ?? null,
            'email'        => $data['email'] ?? null,
            'birthDate'    => $this->formatDate($data['birth_date']),
            'inn'          => $data['inn'] ?? null,
            'snils'        => $data['snils'] ?? null,
            'familyStatus' => [
                'code' => (string)$data['married'],
            ],
            'documentType' => [
                'code' => $data['is_rus'] === true ? '1' : ($data['is_rus'] === false ? '4' : null),
            ],
        ];

        if (isset($data['gender'])) {
            $transformed['genderCode'] = [
                'code' => $data['gender'] === 'male' ? '1' : ($data['gender'] === 'female' ? '2' : null),
            ];
        }

        return $transformed;
    }

    private function formatDate(string $date): string
    {
        $dateTime = \DateTime::createFromFormat('d.m.Y', $date);
        return $dateTime->format('Y-m-d');
    }
}
