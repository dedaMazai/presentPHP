<?php

namespace App\Services\Customer;

use App\Models\Customer\Customer;
use App\Models\Document\DocumentType;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use Carbon\Carbon;

/**
 * Class CustomerRepository
 *
 * @package App\Services\Customer
 */
class CustomerRepository
{
    public function __construct(
        private DynamicsCrmClient $dynamicsCrmClient,
    ) {
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function getById(string $id): Customer
    {
        $data = $this->dynamicsCrmClient->getCustomerById($id);

        return $this->makeCustomer($data);
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function getByPhone(string $phone): array
    {
        $data = $this->dynamicsCrmClient->getCustomerByPhone($phone);

        return $data;
    }

    private function makeCustomer(array $data): Customer
    {
        return new Customer(
            id: $data['id'],
            lastName: $data['lastName'],
            firstName: $data['firstName'],
            middleName: $data['middleName'] ?? null,
            phone: $data['phone'],
            email: $data['email'] ?? null,
            birthDate: isset($data['birthDate'])?
                new Carbon($data['birthDate']):
                Carbon::parse('00:00:00 01.01.1970'),
            documentType: DocumentType::tryFrom($data['documentType']['code'] ?? null)
        );
    }
}
