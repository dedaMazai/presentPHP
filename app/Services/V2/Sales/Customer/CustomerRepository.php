<?php

namespace App\Services\V2\Sales\Customer;

use App\Models\Document\DocumentSubtype;
use App\Models\Role;
use App\Models\V2\Sales\Customer\Customer;
use App\Models\Sales\Gender;
use App\Models\Sales\OwnerType;
use App\Models\Sales\SignStatus;
use App\Models\V2\Sales\Customer\LegalEntityData;
use App\Models\V2\Sales\Customer\Signatory;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use Carbon\Carbon;

/**
 * Class CustomerRepository
 *
 * @package App\Services\V2\Sales\Customer
 */
class CustomerRepository
{
    public function __construct(private readonly DynamicsCrmClient $dynamicsCrmClient)
    {
    }

    public function getByAccountId(string $id): Customer
    {
        $data = $this->dynamicsCrmClient->getCustomerByAccountId($id);

        return $this->makeCustomer($data);
    }


    /**
     * @throws BadRequestException|NotFoundException
     */
    public function getById(string $id): Customer
    {
        $data = $this->dynamicsCrmClient->getCustomerById($id);

        return $this->makeCustomer($data);
    }

    public function makeCustomer(array $data): ?Customer
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
        //(ЮЛ/ИП)
        if (isset($data['customerType']) && $data['customerType']['code'] == "1") {
            // Формируем объект legalEntityData для ЮЛ/ИП
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
                $customer = $this->dynamicsCrmClient->getCustomerByAccountId($data['accountId']);

                if (isset($data['signatoryType']['code']) && $data['signatoryType']['code'] == 1) {
                    $label = 'Директор';
                } elseif (isset($data['signatoryType']['code']) && $data['signatoryType']['code'] == 2) {
                    $label = 'Представитель';
                } else {
                    $label = 'Владелец';
                }
                $signatory = new Signatory(
                    id: $customer['id'],
                    fullName: $customer['nameFull'] ?? $customer['name'],
                    label: $label,
                    signatory: isset($data['confidant']) ? 'owner' : 'signatory',
                );
            }
        }


        return new Customer(
            id: $data['contactId'] ?? null,
            contactId: $data['contactId'] ?? null,
            jointOwnerId: $data['id'] ?? null,
            lastName: $data['lastName'] ?? null,
            firstName: $data['firstName'] ?? null,
            middleName: $data['middleName'] ?? null,
            gender: Gender::tryFrom($data['genderCode']['code'] ?? ''),
            phone: $data['phone'] ?? null,
            phoneHome: $data['phoneHome'] ?? null,
            email: $data['email'] ?? null,
            birthDate: isset($data['birthDate']) ? new Carbon($data['birthDate']) : null,
            birthPlace: $data['birthPlace'] ?? null,
            documentType: DocumentSubtype::tryFrom($data['documentType']['code'] ?? ''),
            documentSeries: $data['documentSeries'] ?? null,
            documentNumber: $data['documentNumber'] ?? null,
            documentGiveOut: $data['documentGiveOut'] ?? null,
            documentDate: isset($data['documentDate']) ? new Carbon($data['documentDate']) : null,
            subdivisionCode: $data['subdivisionCode'] ?? null,
            addressRegistration: $data['addressRegistration'] ?? null,
            citizenship: $data['citizenShip'] ?? null,
            inn: $data['inn'] ?? null,
            snils: $data['snils'] ?? null,
            part: $part,
            esValidityDate: isset($data['esValidityDate']) ? new Carbon($data['esValidityDate']) : null,
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
            //(ЮЛ/ИП)
            legalEntityData: $legalEntityData ?? null,
            //Подписант
            signatory: $signatory ?? null,
        );
    }

    public function createEmptyCustomer(): Customer
    {
        return new Customer(
            id: null,
            contactId: null,
            jointOwnerId: null,
            lastName: '',
            firstName: '',
            middleName: null,
            gender: null,
            phone: null,
            phoneHome: null,
            email: null,
            birthDate: null,
            birthPlace: null,
            documentType: null,
            documentSeries: null,
            documentNumber: null,
            documentGiveOut: null,
            documentDate: null,
            subdivisionCode: null,
            addressRegistration: null,
            citizenship: null,
            inn: null,
            snils: null,
            part: null,
            esValidityDate: null,
            signStatus: null,
            sdSignatureDocPageUrl: null,
            sdSignatureAppPageUrl: null,
            sdSignatureIsExistDoc: null,
            sdSignatureIsExistApp: null,
            ownerType: null,
            ownerTypeComment: null,
            uin: null,
            role: null,
            isDepositor: null,
            isCustomer: null,
            label: null,
            customerType: null,
            legalEntityData: null,
            signatory: null,
        );
    }
}
