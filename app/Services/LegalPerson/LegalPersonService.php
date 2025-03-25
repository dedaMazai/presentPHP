<?php

namespace App\Services\LegalPerson;

use App\Http\Api\External\V1\Requests\LegalPerson\AddAccountToDemandRequest;
use App\Http\Api\External\V1\Requests\LegalPerson\UpdateLegalPersonRequest;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\LegalPerson\Dto\LegalPersonDto;
use App\Services\LegalPerson\ExternalApi\ExternalClient;

/**
 * Class LegalPersonService
 *
 * @package App\Services\LegalPerson
 */
class LegalPersonService
{
    public function __construct(
        private DynamicsCrmClient $dynamicsCrmClient,
        private ExternalClient $client,
    ) {
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function checkInn(int $inn): ?LegalPersonDto
    {
        $response = $this->dynamicsCrmClient->checkInn($inn);

        return $this->createDtoFromResponse($inn, $response);
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function updateLegalPerson(string $id, UpdateLegalPersonRequest $request): ?LegalPersonDto
    {
        $data = [];
        $request->name ? $data['name'] = $request->name : null;
        $request->inn ? $data['inn'] = $request->inn : null;
        $request->ogrn ? $data['ogrn'] = $request->ogrn : null;
        $request->kpp ? $data['kpp'] = $request->kpp : null;
        $request->address_legal ? $data['addressJuristic'] = $request->address_legal : null;
        $request->phone ? $data['phone'] = $request->phone : null;
        $request->mail ? $data['mail'] = $request->mail : null;

        $response = $this->dynamicsCrmClient->putAccounts($id, $data);

        return $this->createDtoFromResponse($id, $response);
    }

    public function fill(string $inn): ?LegalPersonDto
    {
        $response = $this->client->getAccountFill($inn);

        return $this->createDtoFromDadataResponse($inn, $response);
    }

    public function addAccountToDemand(string $id, array $data, string $userId)
    {
        $externalRequest = [
            'name' => $data['name'],
            'inn' => $data['inn'],
            'phone' => $data['phone'],
            'mail' => $data['mail'],
            'ogrn' => $data['ogrn'],
            'addressJuristic' => $data['address_legal']
        ];

        if ($data['account_type'] == 'legal') {
            $externalRequest['kpp'] = $data['kpp'];
        } elseif ($data['account_type'] == 'individual') {
            $externalRequest['accountCategory']['code'] = 6;
            $externalRequest['primaryContact']['code'] = $userId;
        }

        $response = $this->dynamicsCrmClient->createAccount($externalRequest);

        $putDemandRequest = [
            'customerId' => $response['id'],
            'customerType' => [
                'code' => 1
            ],
            'DepositorUrId' => $response['id']
        ];

        $putDemandResponse = $this->dynamicsCrmClient->putDemand($id, $putDemandRequest);

        if ($data['account_type'] == 'legal') {
            $jointOwnerId = $putDemandResponse['jointOwners'][0]['id'];
            $putJointOwnerRequest = [
                'confidantId' => $userId
            ];

            $this->dynamicsCrmClient->putJointOwner($jointOwnerId, $putJointOwnerRequest);
        }

        return $this->dynamicsCrmClient->getDemandByUserId($id, $userId);
    }

    private function createDtoFromResponse($id, $response)
    {
        $accountType = null;

        if (isset($response['accountCategory'])) {
            if ($response['accountCategory']['code'] == 6) {
                $accountType = 'individual';
            } else {
                $accountType = 'legal';
            }
        } else {
            if (strlen($response['inn']) == 10) {
                $accountType = 'legal';
            } elseif (strlen($response['inn']) == 12) {
                $accountType = 'individual';
            }
        }

        return new LegalPersonDto(
            id: $response['id'],
            name: $response['name'] ?? null,
            inn: $response['inn'] ?? null,
            ogrn: $response['ogrn'] ?? null,
            kpp: $response['kpp'] ?? null,
            address_legal: $response['addressJuristic'] ?? null,
            phone: $response['phone'] ?? null,
            mail: $response['mail'] ?? null,
            account_type: $accountType,
            message: $response['message'],
            type_message: $response['typeMessage']
        );
    }

    private function createDtoFromDadataResponse($id, $response)
    {
        $accountType = null;
        $dataType = $response['suggestions'][0]['data']['type'];
        $data = $response['suggestions'][0]['data'];
        if ($dataType == 'INDIVIDUAL') {
            $accountType = 'individual';
        } elseif ($dataType == 'LEGAL') {
            $accountType = 'legal';
        }

        return new LegalPersonDto(
            id: $id,
            name: $data['value'] ?? null,
            inn: $data['inn'] ?? null,
            ogrn: $data['ogrn'] ?? null,
            kpp: $data['kpp'] ?? null,
            address_legal: $data['address']['value'] ?? null,
            phone: $data['phones'][0]['value'] ?? null,
            mail: $data['emails'][0]['value'] ?? null,
            account_type: $accountType,
            message: null,
            type_message: null
        );
    }
}
