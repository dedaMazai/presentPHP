<?php

namespace App\Services\V2\Sales\Customer;

use App\Models\Document\DocumentSubtype;
use App\Models\Role;
use App\Models\Sales\Gender;
use App\Models\Sales\OwnerType;
use App\Models\Sales\SignStatus;
use App\Models\V2\Sales\Customer\Customer;
use App\Models\V2\Sales\Customer\CustomerContractConfidant;
use App\Models\V2\Sales\Customer\LegalEntityData;
use App\Models\V2\Sales\Customer\Signatory;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use Carbon\Carbon;

class CustomerContractConfidantRepository
{
    public function __construct(private readonly DynamicsCrmClient $dynamicsCrmClient)
    {
    }

    /**
     * @throws BadRequestException|NotFoundException
     */
    public function getById(string $id, array $customer = null): CustomerContractConfidant
    {
        $data = $this->dynamicsCrmClient->getCustomerById($id);

        return $this->makeCustomer($data, $customer);
    }

    public function makeCustomer(array $data, array $customer): ?CustomerContractConfidant
    {
        if (isset($data['part'])) {
            if (str_starts_with($data['part'], "_")) {
                $part = null;
            } else {
                $part = $data['part'];
            }
        } else {
            $part = null;
        }

        if (($data['typeMessage'] ?? null) === 2) {
            return null;
        }

        if (isset($data['customerType']) && $data['customerType']['code'] == "1") {
            $legalEntityData = null;
            $signatory = null;
            $legalEntityData = new LegalEntityData(
                accountId: $data['accountId'] ?? null,
                jointOwnerId: $data['id'] ?? null,
                accountType: isset($data['confidant']) ? "legal" : "individual",
                name: $data['name'] ?? null,
                inn: $data['inn'] ?? null,
            );

            if ($data['accountId']) {
                $customerSignatory = $this->dynamicsCrmClient->getCustomerByAccountId($data['accountId']);

                if (isset($data['signatoryType']['code']) && $data['signatoryType']['code'] == 1) {
                    $label = 'Директор';
                } elseif (isset($data['signatoryType']['code']) && $data['signatoryType']['code'] == 2) {
                    $label = 'Представитель';
                } else {
                    $label = 'Владелец';
                }
                $signatory = new Signatory(
                    id: $customerSignatory['id'],
                    fullName: $customerSignatory['nameFull'] ?? $customerSignatory['name'],
                    label: $label,
                    signatory: isset($data['confidant']) ? 'owner' : 'signatory',
                );
            }
        }

        if (!isset($data['confidant'])) {
            $fullName = $data['primaryContact']['name'];
        } else {
            $fullName = trim(
                ($customer['lastName'] ?? '') .
                ' ' . ($customer['firstName'] ?? '') .
                ' ' . ($customer['middleName'] ?? '')
            );
        }

        return new CustomerContractConfidant(
            id: $data['primaryContact']['code'] ?? null,
            contactId: $data['contactId'] ?? null,
            jointOwnerId: $data['id'] ?? null,
            fullName: $fullName,
            gender: Gender::tryFrom($data['genderCode']['code'] ?? ''),
            phone: $data['phone'] ?? null,
            phoneHome: $data['phoneHome'] ?? null,
            email: $data['email'] ?? null,
            birthDate: isset($data['birthDate']) && !empty($data['birthDate'])
                ? new Carbon($data['birthDate'])
                : null,
            birthPlace: isset($data['birthPlace']) ?? null,
            documentType: DocumentSubtype::tryFrom($data['documentType']['code'] ?? ''),
            documentSeries: $data['documentSeries'] ?? null,
            documentNumber: $data['documentNumber'] ?? null,
            documentGiveOut: $data['documentGiveOut'] ?? null,
            documentDate: isset($data['documentDate'])
                ? new Carbon($data['documentDate'])
                : null,
            subdivisionCode: $data['subdivisionCode'] ?? null,
            addressRegistration: $data['addressRegistration'] ?? null,
            citizenship: $data['citizenShip'] ?? null,
            inn: $data['inn'] ?? null,
            snils: $data['snils'] ?? null,
            part: $part,
            esValidityDate: isset($data['esValidityDate'])
                ? new Carbon($data['esValidityDate'])
                : null,
            signStatus: SignStatus::tryFrom($data['statusSign']['code'] ?? ''),
            sdSignatureDocPageUrl: $data['sdSignatureDocPageUrl'] ?? null,
            sdSignatureAppPageUrl: $data['sdSignatureAppPageUrl'] ?? null,
            sdSignatureIsExistDoc: $data['sdSignatureIsExistDoc'] ?? null,
            sdSignatureIsExistApp: $data['sdSignatureIsExistApp'] ?? null,
            ownerType: OwnerType::tryFrom($data['ownerType']['code'] ?? ''),
            ownerTypeComment: $data['ownerTypeComment'] ?? null,
            uin: $data['uin'] ?? null,
            role: Role::tryFrom($data['roleCode']['code'] ?? ''),
            isDepositor: $data['isDepositor'] ?? null,
            isCustomer: $data['isCustomer'] ?? null,
            label: $data['label'] ?? null,
            customerType: $data['customerType'] ?? [],
            // (ЮЛ/ИП)
            legalEntityData: $legalEntityData ?? null,
            // Подписант
            signatory: $signatory ?? null
        );
    }
}
