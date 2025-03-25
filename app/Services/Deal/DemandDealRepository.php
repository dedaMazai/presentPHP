<?php

namespace App\Services\Deal;

use App\Models\Sales\Demand\DemandStatus;
use App\Models\User\User;
use App\Services\Deal\Dto\SetBankDto;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;

/**
 * Class DemandDealRepository
 *
 * @package App\Services\Deal
 */
class DemandDealRepository
{
    public function __construct(private DynamicsCrmClient $dynamicsCrmClient,)
    {
    }

    /**
     * @param string $demandId
     * @param string $finishId
     * @return mixed|null
     * @throws \App\Services\DynamicsCrm\Exceptions\BadRequestException
     * @throws \App\Services\DynamicsCrm\Exceptions\NotFoundException
     */
    public function addFinishVariant(string $demandId, string $finishId)
    {
        return $this->dynamicsCrmClient->addFinishVariant($demandId, $finishId);
    }

    /**
     * @param string $demandId
     * @param array $banksId
     * @return array|null
     * @throws \App\Services\DynamicsCrm\Exceptions\BadRequestException
     * @throws \App\Services\DynamicsCrm\Exceptions\NotFoundException
     */
    public function putDemand(string $demandId, array $banksId)
    {
        return $this->dynamicsCrmClient->setDemandDealBank($demandId, $banksId);
    }

    /**
     * @return array|null
     * @throws \App\Services\DynamicsCrm\Exceptions\BadRequestException
     * @throws \App\Services\DynamicsCrm\Exceptions\NotFoundException
     */
    public function setDealBank(string $demandId, string $bank, string $bankId)
    {
        return $this->dynamicsCrmClient->setDealBank($demandId, $bank, $bankId);
    }

    /**
     * @param string $demandId
     * @param array $depositor
     * @return array|null
     * @throws \App\Services\DynamicsCrm\Exceptions\BadRequestException
     * @throws \App\Services\DynamicsCrm\Exceptions\NotFoundException
     */
    public function putDepositor(string $demandId, array $depositor)
    {
        return $this->dynamicsCrmClient->setDemandDealDepositor($demandId, $depositor);
    }

    /**
     * @param string $demandId
     * @param array $characteristicSaleIds
     * @return array|null
     * @throws \App\Services\DynamicsCrm\Exceptions\BadRequestException
     * @throws \App\Services\DynamicsCrm\Exceptions\NotFoundException
     */
    public function characteristicSalesClear(string $demandId, array $characteristicSaleIds)
    {
        return $this->dynamicsCrmClient->setCharacteristicSalesClear($demandId, $characteristicSaleIds);
    }

    /**
     * @param string $demandId
     * @param array $characteristicSaleIds
     * @return array|null
     * @throws \App\Services\DynamicsCrm\Exceptions\BadRequestException
     * @throws \App\Services\DynamicsCrm\Exceptions\NotFoundException
     */
    public function characteristicSalesDemand(string $demandId, array $characteristicSaleIds)
    {
        return $this->dynamicsCrmClient->setCharacteristicSalesDemand($demandId, $characteristicSaleIds);
    }

    /**
     * @param SetBankDto $dto
     * @return array|null
     * @throws \App\Services\DynamicsCrm\Exceptions\BadRequestException
     * @throws \App\Services\DynamicsCrm\Exceptions\NotFoundException
     */
    public function setBankId(SetBankDto $dto, User $user)
    {
        $bank = $this->dynamicsCrmClient->setBankId($dto);

        if ($dto->isSberClient != null) {
            $this->dynamicsCrmClient->setUserSberClient($user, $dto->isSberClient);
        }

        $demand = $this->dynamicsCrmClient->getDemandById($dto->demandId, $user);
        $letterOfCreditBankId = $demand['letterOfCreditBankId'];
        $property = $this->dynamicsCrmClient->getPropertyById($demand['articleId']);
        $escrowBankId = $property['escrowBankId'] ?? null;
        $user = $this->dynamicsCrmClient->getCustomerById($user->crm_id);
        $isSberClient = $user['isSberClient'];

        $nextStepsBanksInfo = '';
        $sberBankId = '5f399d29-60b9-40f6-8d1a-935fbfa98977';
        $wtbBankId = '4616b662-1606-459d-b23b-fbf539d51ef9';
        $otherBankId = '0af0ffcb-060d-ea11-942d-005056bf3b92';
        $domrfBankId = 'd417af4e-bc76-e711-9402-005056bf3b92';
        $alfaBankId = '5d5c6d4f-bb72-e711-9402-005056bf3b92';

        if ($escrowBankId && $letterOfCreditBankId == null) {
            $nextStepsBanksInfo = null;
        } elseif ($escrowBankId == null && $letterOfCreditBankId == $sberBankId && $isSberClient == true) {
            $nextStepsBanksInfo = config('bank_info_message.instruction_for_opening_from_manager');
        } elseif ($escrowBankId == null && $letterOfCreditBankId == $sberBankId && $isSberClient == false) {
            $nextStepsBanksInfo = config('bank_info_message.contact_the_sber');
        } elseif ($escrowBankId == null && $letterOfCreditBankId == $wtbBankId) {
            $nextStepsBanksInfo = config('bank_info_message.two_payment_receipts');
        } elseif ($escrowBankId == null && $letterOfCreditBankId == $otherBankId) {
            $nextStepsBanksInfo = config('bank_info_message.contact_the_manager');
        } elseif ($escrowBankId == $sberBankId && $letterOfCreditBankId == $wtbBankId) {
            $nextStepsBanksInfo = config('bank_info_message.sber_escrow_documents');
        } elseif ($escrowBankId == $sberBankId && $letterOfCreditBankId == $sberBankId && $isSberClient == true) {
            $nextStepsBanksInfo = config('bank_info_message.managers_prepare_the_documents');
        } elseif ($escrowBankId == $sberBankId && $letterOfCreditBankId == $sberBankId && $isSberClient == false) {
            $nextStepsBanksInfo = config('bank_info_message.sber_escrow');
        } elseif ($escrowBankId == $sberBankId && $letterOfCreditBankId == $otherBankId) {
            $nextStepsBanksInfo = config('bank_info_message.contact_the_manager');
        } elseif ($escrowBankId == $domrfBankId && $letterOfCreditBankId == $wtbBankId) {
            $nextStepsBanksInfo = config('bank_info_message.domrf_two_receipts');
        } elseif ($escrowBankId == $domrfBankId && $letterOfCreditBankId == $sberBankId && $isSberClient == true) {
            $nextStepsBanksInfo = config('bank_info_message.domrf_with_instruction');
        } elseif ($escrowBankId == $domrfBankId && $letterOfCreditBankId == $sberBankId && $isSberClient == false) {
            $nextStepsBanksInfo = config('bank_info_message.domrf_ofice');
        } elseif ($escrowBankId == $domrfBankId && $letterOfCreditBankId == $otherBankId) {
            $nextStepsBanksInfo = config('bank_info_message.contact_the_manager');
        } elseif ($escrowBankId == $alfaBankId && $letterOfCreditBankId == $alfaBankId) {
            $nextStepsBanksInfo = config('bank_info_message.alfa_requisites');
        } elseif ($escrowBankId == $alfaBankId && $letterOfCreditBankId == $wtbBankId) {
            $nextStepsBanksInfo = config('bank_info_message.alfa_two_receipts');
        } elseif ($escrowBankId == $alfaBankId && $letterOfCreditBankId == $sberBankId && $isSberClient == true) {
            $nextStepsBanksInfo = config('bank_info_message.alfa_managers');
        } elseif ($escrowBankId == $alfaBankId && $letterOfCreditBankId == $sberBankId && $isSberClient == false) {
            $nextStepsBanksInfo = config('bank_info_message.alfa_ofice');
        } elseif ($escrowBankId == $alfaBankId && $letterOfCreditBankId == $otherBankId) {
            $nextStepsBanksInfo = config('bank_info_message.contact_the_manager');
        }

        return $nextStepsBanksInfo;
    }

    /**
     * @param $demandId
     * @return array|null
     * @throws \App\Services\DynamicsCrm\Exceptions\BadRequestException
     * @throws \App\Services\DynamicsCrm\Exceptions\NotFoundException
     */
    public function clearArticleId($demandId)
    {
        return $this->dynamicsCrmClient->clearArticleId($demandId);
    }

    /**
     * @param $user
     * @return array|null
     * @throws \App\Services\DynamicsCrm\Exceptions\BadRequestException
     * @throws \App\Services\DynamicsCrm\Exceptions\NotFoundException
     */
    public function getDemandByOpenStatus($user)
    {
        return $this->dynamicsCrmClient->getDemandsByStatus($user, DemandStatus::open());
    }

    /**
     * @param string $leadId
     * @param string $jointOwnerId
     * @return void
     * @throws \App\Services\DynamicsCrm\Exceptions\BadRequestException
     * @throws \App\Services\DynamicsCrm\Exceptions\NotFoundException
     */
    public function deleteLead(string $leadId, string $jointOwnerId)
    {
        $this->dynamicsCrmClient->deleteJointOwner($leadId, $jointOwnerId);
    }

    /**
     * @param string $leadId
     * @param string $customerId
     * @return void
     * @throws \App\Services\DynamicsCrm\Exceptions\BadRequestException
     * @throws \App\Services\DynamicsCrm\Exceptions\NotFoundException
     */
    public function createLeadCustomer(string $leadId, string $customerId)
    {
        $this->dynamicsCrmClient->setJointOwnerCustomer($leadId, $customerId);
    }

    /**
     * @param string $id
     * @return array|null
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function getCharacteristicSales(string $id)
    {
        $data = $this->dynamicsCrmClient->getCharacteristicSalesByPropertyId($id);

        return $data;
    }
}
