<?php

namespace App\Services\Sales\Deal;

use App\Models\Sales\Deal;
use App\Models\Sales\Demand\DemandBookingType;
use App\Models\Sales\StepMapper;
use App\Services\Sales\Deal\Dto\CreateDealDto;
use Carbon\Carbon;

/**
 * Class DealService
 *
 * @package App\Services\Sales
 */
class DealService
{
    public function createDeal(CreateDealDto $dto): void
    {
        Deal::create([
            'user_id' => $dto->user->id,
            'demand_id' => $dto->demandId,
            'demand_status' => $dto->demandStatus,
            'demand_booking_type' => $dto->demandBookingType,
            'property_id' => $dto->propertyBookingDto->id,
            'is_escrow' => $dto->propertyBookingDto->isEscrow ?? false,
            'current_step' => StepMapper::STEP_TERMS,
            'project_id' => $dto->propertyBookingDto->project?->id,
            'initial_begin_date' => $dto->initialBeginDate,
            'initial_end_date' => $dto->initialEndDate,
        ]);
    }

    public function setIsEscrowBankClient(Deal $deal, bool $isEscrowBankClient): void
    {
        $deal->update([
            'is_escrow_bank_client' => $isEscrowBankClient,
        ]);
    }

    public function setCurrentStep(Deal $deal, string $currentStep): void
    {
        $deal->update([
            'current_step' => $currentStep,
        ]);
    }

    public function setMortgageDemandId(Deal $deal, ?string $mortgageDemandId): void
    {
        $deal->update([
            'mortgage_demand_id' => $mortgageDemandId,
        ]);
    }

    public function setContractReadAt(Deal $deal, Carbon $contractReadAt): void
    {
        $deal->update([
            'contract_read_at' => $contractReadAt,
        ]);
    }

    public function setDemandBookingType(Deal $deal, DemandBookingType $bookingType): void
    {
        $deal->update([
            'demand_booking_type' => $bookingType,
        ]);
    }
}
