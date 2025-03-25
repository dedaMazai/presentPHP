<?php

namespace App\Services\V2\Sales\Customer;

use App\Models\Document\DocumentSubtype;
use App\Models\Role;
use App\Models\Sales\Customer\Customer;
use App\Models\Sales\Gender;
use App\Models\Sales\OwnerType;
use App\Models\Sales\SignStatus;
use App\Models\User\User;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\V2\Sales\Customer\Dto\CombinedCustomerDto;
use App\Services\V2\Sales\Customer\Dto\CustomerContractJointOwnerInfoDto;
use App\Services\V2\Sales\Customer\Dto\LegalEntityDto;
use Carbon\Carbon;

/**
 * Class CustomerRepository
 *
 * @package App\Services\Sales\Customer
 */
class CustomerContractJointOwnerInfoRepository
{
    public function __construct(private DynamicsCrmClient $dynamicsCrmClient,)
    {
    }

    /**
     * @throws BadRequestException
     */
    public function getById(string $id): Customer
    {
        $data = $this->dynamicsCrmClient->getCustomerById($id);

        return $this->makeCustomer($data);
    }

    public function getByAccountId(string $id): Customer
    {
        $data = $this->dynamicsCrmClient->getCustomerByAccountId($id);

        return $this->makeCustomer($data);
    }

    public function getByCustomer(array $customer, User $user)
    {
        if ($customer['customerType']['code'] == 1) {
            if (!isset($customer['confidant'])) { //!isset
                $data = $this->dynamicsCrmClient->getCustomerById($user->crm_id);
                $data['jointOwnerId'] = $customer['id'];
                return $this->makeCustomer($data);
            } elseif (isset($customer['confidant'])) { //isset
                $accountCustomer = $this->dynamicsCrmClient->getCustomerByAccountId($customer['accountId']);
                $data = $this->dynamicsCrmClient->getCustomerById($customer['primaryContact']['code']);
                $data['jointOwnerId'] = $customer['id'];
                $legalEntityData = $this->legalEntity($accountCustomer);
                $individualOwner = $this->makeCustomer($data);
                return new CombinedCustomerDto(
                    legalEntityData: $legalEntityData,
                    individualOwner: $individualOwner
                );
            }
        } else {
            $data = $this->dynamicsCrmClient->getCustomerById($customer['contactId']);
            $data['jointOwnerId'] = $customer['id'];
            return $this->makeCustomer($data);
        }
    }

    private function legalEntity(array $data): LegalEntityDto
    {
        return new LegalEntityDto(
            id: $data['id'],
            name: $data['name'],
            inn: $data['inn'] ?? null,
            ogrn: $data['ogrn'] ?? null,
            kpp: $data['kpp'] ?? null,
            address_legal: $data['addressJuristic'] ?? null,
            phone: $data['phone'] ?? null,
            mail: $data['mail'] ?? null,
        );
    }

    public function makeCustomer(array $data): Customer
    {
        return new Customer(
            id: $data['id'],
            contactId: $data['contactId'] ?? null,
            jointOwnerId: $data['jointOwnerId'] ?? null,
            lastName: $data['lastName'] ?? '',
            firstName: $data['firstName'] ?? '',
            middleName: $data['middleName'] ?? null,
            gender: Gender::tryFrom($data['genderCode']['code'] ?? ''),
            phone: $data['phone'] ?? null,
            phoneHome: $data['phoneHome'] ?? null,
            email: $data['email'] ?? null,
            birthDate: isset($data['birthDate'])?
                new Carbon($data['birthDate']):
                Carbon::parse('00:00:00 01.01.1970'),
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
            part: $data['part'] ?? null,
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
            married: $data['familyStatus']['code'] ?? null,
            label: $data['label'] ?? null
        );
    }
}
