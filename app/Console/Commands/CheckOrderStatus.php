<?php

namespace App\Console\Commands;

use App\Mail\CrmFailedMail;
use App\Models\Contract\ContractType;
use App\Models\Sales\PayBookingTime;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;

use App\Services\Sberbank\SberbankClient;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Class CheckOrderStatus
 *
 * @package App\Console\Commands
 */
class CheckOrderStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:check-order-status';

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function handle(
        DynamicsCrmClient $dynamicsCrmClient,
        SberbankClient $client,
    ): void {
        $payBookingTime = PayBookingTime::where('status', '=', 'payment_await')->get();

        foreach ($payBookingTime as $payBooking) {
            if (Carbon::parse($payBooking->time_to_pay) < Carbon::now()) {
                $payBooking->update(['status' => 'expired']);
            } else {
                $status = $client->checkOrderStatus($payBooking->order_id)['orderStatus'];

                if ($status == 2) {
                    $body = [
                        'contractReservPaymentStatus' => [
                            'code' => 4
                        ]
                    ];

                    $dynamicsCrmClient->changeDemandSubType($payBooking->crm_id, $body);
                    $payBooking->update(['status' => 'paid']);
                }
            }
        }
    }
}
