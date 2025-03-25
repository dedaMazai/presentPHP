<?php

namespace App\Http\Resources\Sales\Contract;

use App\Http\Resources\Sales\StageCollection;
use App\Http\Resources\V2\Sales\ContractJointOwnerCollection;
use App\Http\Resources\V2\Sales\Demand\OwnerTypeResource;
use App\Http\Resources\V2\Sales\Demand\SalesSchemeResource;
use App\Http\Resources\V2\Sales\JointOwnerCollection;
use App\Http\Resources\V2\Sales\PropertyResource;
use App\Models\V2\Contract\Contract;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailContractResource extends JsonResource
{
    public function toArray($request)
    {

        /** @var Contract $this */
        return [
            'contract_info' => $this->getContractInfo(),
            'contract_number' => $this->getName(),
            'payment_mode' => $this->getPaymentModeCode()?[
                'code' => intval($this->getPaymentModeCode()->value),
                'name' => $this->getPaymentModeCode()->label,
            ]:null,
            'owner_type' => new OwnerTypeResource($this->getOwnership()),
            'contract_date' => $this->getDate(),
            'sales_scheme' => new SalesSchemeResource($this->getArticleOrders()[0]),
            'contract_price' => round($this->getEstimated(), 2),
            'quantity' => $this->getArticleOrders()[0]?->getQuantity()?round($this->getArticleOrders()[0]?->getQuantity(), 2):null,
            'is_contract_approve' => $this->getIsContractApprove(),
            'property' => $this->getProperty() ? new PropertyResource($this->getProperty()) : null,
            'registration_stage' => $this->getRegistrationStage(),
            'is_sign_app_availible' => $this->getIsSignAppAvailible(),
            'hypothec_bank_id' => $this->getHypothecBankId(),
            'letter_of_credit_bank_id' => $this->getLetterOfCreditBankId(),
            'date_of_signing_fact' => $this->getDateOfSigningFact(),
            'electro_reg' => $this->getElectroReg(),
            'electro_reg_info' => $this->getElectroRegInfo(),
            'stages' => new StageCollection($this->getStages()),
            'is_required_sms_code' => false,
            'sms_code_type' => $this->getSmsCodeType(),
            'joint_owners' => $this->getJointOwners() ?new ContractJointOwnerCollection($this->getJointOwners()) : [],
            'draft_contract_document' => new DraftContractDocumentResource($this->getDraftContractDocument()),
            'draft_contract_document_info' => $this->getDraftContractDocumentInfo(),
            'debt' => $this->getDebtPlanSum(),
            'percent_pay' => intval($this->getPercentPay()),
            'days_delay' => $this->getDelayDays(),
            'is_hypothec_sup' => $this->getIsHypothecSup()
        ];
    }
}
