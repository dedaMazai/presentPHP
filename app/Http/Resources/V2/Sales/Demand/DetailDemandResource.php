<?php

namespace App\Http\Resources\V2\Sales\Demand;

use App\Http\Resources\V2\Sales\BorrowerCollection;
use App\Http\Resources\V2\Sales\JointOwnerCollection;
use App\Http\Resources\V2\Sales\LetterOfCreditBankResource;
use App\Http\Resources\V2\Sales\PropertyResource;
use App\Http\Resources\Sales\StageCollection;
use App\Models\V2\Sales\Demand\Demand;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailDemandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // phpcs:disable
        /** @var Demand $this */
        return [
            'id' => $this->getId(),
            'demand_number' => intval($this->getNumber()),
            'payment_mode' => $this->getPaymentMode()?[
                'code' => intval($this->getPaymentMode()->value),
                'name' => $this->getPaymentMode()->label,
            ]:null,
            'owner_type' => new OwnerTypeResource($this->getOwnership()),
            'quantity' => $this->getMainArticleOrder()?->getQuantity(),
            'total_price' => intval($this->getSumOpportunityMinusDiscount()),
            'object_price' =>  $this->getArticlePrice() == $this->getSumOpportunityMinusDiscount() ? null:intval($this->getArticlePrice()),
            'sales_scheme' => new SalesSchemeResource($this->getSalesScheme()),
            'is_tradein_available' => $this->getTradeIn()->isTradeInAvailable(),
            'tradein_info' => $this->getTradeIn()->getTradeInInfo(),
            'is_booking_cancel_available' => $this->getIsBookingCancelAvailable(),
            'is_mortgage_available' => $this->getIsMortgageAvailable(),
            'is_mortgage_online_available' => $this->getIsMortgageOnlineAvailable(),
            'mortgage_online_info' => $this->getMortgageOnlineInfo(),
            'is_early_previously_owners' => $this->getIsEarlyPreviouslyOwners(),
            'is_available_change_payment_mode' => $this->getIsAvailableChangePaymentMode(),
            'change_payment_mode_disable_info' => $this->getChangePaymentModeDisableInfo(),
            'instalments_info' => $this->getInstalmentsInfo(),
            'is_availible_instalments' => $this->getIsAvailibleInstalments(),
            'is_payment_booking_availible' => $this->getIsPaymentBookingAvailible(),
            'next_steps_banks_info' => $this->getNextStepsBanksInfo(),
            'property' => $this->getProperty() ? new PropertyResource($this->getProperty()) : null,
            'joint_owners' => $this->getJointOwners() ?new JointOwnerCollection($this->getJointOwners()) : [],
            'borrowers' => $this->getBorrowers() ? new BorrowerCollection($this->getBorrowers()) : null,
            'is_borrowers_availible' => $this->getIsBorrowersAvailible(),
            'payment_booking' => $this->getPaymentBooking() != [] ? $this->getPaymentBooking() : null,
            'instalments' => $this->getInstalments(),
            'letter_of_credit_bank' => $this->getLetterOfCreditBank() ?
                new LetterOfCreditBankResource($this->getLetterOfCreditBank()): null,
            'is_transaction_terms_full' => $this->getIsTransactionTermsFull(),
            'transaction_terms_full_info' => $this->getTransactionTermsFullInfo(),
            'stages' => new StageCollection($this->getStages()),
        ];
    }
}
