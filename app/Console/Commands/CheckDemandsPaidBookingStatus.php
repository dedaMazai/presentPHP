<?php

namespace App\Console\Commands;

use App\Models\Sales\Deal;
use App\Models\Sales\Demand\DemandBookingType;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\Sales\Deal\DealService;
use App\Services\Sales\Demand\DemandRepository;
use App\Services\Sales\Demand\DemandService;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

/**
 * Class CheckDemandsPaidBookingStatus
 *
 * @package App\Console\Commands
 */
class CheckDemandsPaidBookingStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demands:check-paid-booking';

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function handle(
        DemandRepository $demandRepository,
        DemandService $demandService,
        DealService $dealService,
    ): void {
        $currentDateTime = new Carbon();

        $deals = Deal::all();
        foreach ($deals as $deal) {
            if ($deal->demand_booking_type->equals(DemandBookingType::paid()) && !$deal->is_booking_paid) {
                try {
                    $demand = $demandRepository->getDemandById($deal->demand_id, $deal->user);
                } catch (Exception) {
                    continue;
                }

                $beginDate = $demand->getBeginDate()?->clone();
                if ($demand->getBookingType()->equals(DemandBookingType::free())) {
                    $dealService->setDemandBookingType($deal, DemandBookingType::free());
                } elseif ($beginDate && $currentDateTime->greaterThan($beginDate->addRealHour())) {
                    $demandService->setFreeBooking($demand);

                    $dealService->setDemandBookingType($deal, DemandBookingType::free());
                }
            }
        }

        $this->info('Demands paid booking status was checked.');
    }
}
