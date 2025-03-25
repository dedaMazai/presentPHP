<?php

namespace App\Http\Api\External\V1\Controllers\Sales;

use App\Models\PaymentMethodType;
use App\Models\Project\Project;
use App\Models\Sales\PayBookingTime;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\Payment\Dto\CreateBookingPaymentDto;
use App\Services\Payment\Exceptions\BadRequestException as PaymentBadRequestException;
use App\Services\Payment\PaymentService;
use App\Services\Sales\Demand\DemandRepository;
use App\Services\Sales\Demand\DemandService;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PaymentController
 *
 * @package App\Http\Api\External\V1\Controllers\Sales
 */
class PaymentController extends BaseSalesController
{
    public function __construct(
        private readonly DemandRepository  $demandRepository,
        private readonly DemandService     $demandService,
        private readonly DynamicsCrmClient $dynamicsCrmClient,
    ) {
        parent::__construct($this->demandRepository);
    }

    /**
     * @throws PaymentBadRequestException
     * @throws AuthenticationException
     * @throws Exception
     */
    public function payBooking(string $id, PaymentService $service): Response
    {
        // phpcs:disable
        $demand = $this->findDemand($id);

        $payBookingTime = PayBookingTime::where("crm_id", "=", $demand->getId())
            ->where("status", "=", "payment_await")->first();

        if ($payBookingTime !== null) {
            return $this->response(["url" => $payBookingTime->payment_url]);
        }

        $demandSubTypeCode = $demand->getBookingType()->value;
        $articleId = $demand->getArticleId();
        $contractId = null;

        $projectTrue = Project::whereJsonContains("booking_property", [
            [
                "crm_id" => $demand->getProperty()?->getAddress()->getId()
            ]
        ])->whereJsonContains("booking_property", [
            [
                "is_premium" => true
            ]
        ])->first();

        $projectFalse = Project::whereJsonContains("booking_property", [
            [
                "crm_id" => $demand->getProperty()?->getAddress()->getId()
            ]
        ])->first();

        $amount = 15000;
        $cost = 0;

        if ($projectTrue !== null) {
            foreach ($projectTrue->booking_property as $item) {
                if ($item["crm_id"] == $demand->getProperty()?->getAddress()->getId()) {
                    $cost = $item["paid_booking_cost"];
                    break;
                }
            }

            $amount = $cost;
        } elseif ($projectFalse !== null) {
            foreach ($projectFalse->booking_property as $item) {
                if ($item["crm_id"] == $demand->getProperty()?->getAddress()->getId()) {
                    $cost = $item["paid_booking_cost"];
                    break;
                }
            }

            $amount = $cost;
        }

        try {
            if ($demandSubTypeCode == 1) {
                if ($projectTrue !== null) {
                    $data = [
                        "customerId" => $this->getAuthUser()->crm_id,
                        "demandId" => $id,
                        "articleId" => $demand->getProperty()?->getId(),
                        "demandSubType" => [
                            "code" => 16
                        ]
                    ];
                } elseif ($projectFalse !== null) {
                    $data = [
                        "customerId" => $this->getAuthUser()->crm_id,
                        "demandId" => $id,
                        "articleId" => $demand->getProperty()?->getId(),
                        "demandSubType" => [
                            "code" => 2
                        ]
                    ];
                } else {
                    $data = [
                        "customerId" => $this->getAuthUser()->crm_id,
                        "demandId" => $id,
                        "articleId" => $demand->getProperty()?->getId(),
                        "demandSubType" => [
                            "code" => 2
                        ]
                    ];

                }

                $contract = $this->dynamicsCrmClient->createBookingContractFromData($data);
                $contractId = $contract["id"];
            } elseif ($demandSubTypeCode == 2) {
                if ($demand->getContractReservPaymentStatusCode() != 1) {
                    throw new Exception('Error', 400);
                }

                $contract = $this->dynamicsCrmClient->getContractsByPropertyId($this->getAuthUser()->crm_id, $demand->getArticleId());
                $countOfContracts = collect($contract['contracts'] ?? [])?->where("serviceMain.code", "=", "030041")->count();

                if ($countOfContracts == 0 ) {
                    $data = [
                        "customerId" => $this->getAuthUser()->crm_id,
                        "demandId" => $id,
                        "articleId" => $demand->getProperty()?->getId(),
                        "demandSubType" => [
                            "code" => 2
                        ]
                    ];

                    $contract = $this->dynamicsCrmClient->createBookingContractFromData($data);
                    $contractId = $contract["id"];
                } else {
                    throw new Exception("Error", 400);
                }
            } elseif ($demandSubTypeCode == 16) {
                if ($demand->getContractReservPaymentStatusCode() != 1) {
                    throw new Exception("Error", 400);
                }

                $contract = $this->dynamicsCrmClient->getContractsByPropertyId($this->getAuthUser()->crm_id, $demand->getArticleId());
                $countOfContracts = collect($contract["contracts"] ?? [])?->where("serviceMain.code", "=", "030044")->count();

                if ($countOfContracts == 0 ) {
                    $data = [
                        "customerId" => $this->getAuthUser()->crm_id,
                        "demandId" => $id,
                        "articleId" => $demand->getProperty()?->getId(),
                        "demandSubType" => [
                            "code" => 2
                        ]
                    ];

                    $contract = $this->dynamicsCrmClient->createBookingContractFromData($data);
                    $contractId = $contract["id"];
                } else {
                    throw new Exception("Error", 400);
                }
            } elseif ($demandSubTypeCode == 8) {
                $contract = $this->dynamicsCrmClient->getContractsByPropertyId($this->getAuthUser()->crm_id, $demand->getArticleId());
                $contract = collect($contract["contracts"] ?? [])->where("serviceMain.code", "=", "030043")->first->toArray();
                $contractId = $contract["id"];
                $amount = $contract["estimated"] ?? 0;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 400);
        }

        $demand = $this->findDemand($id);

        $bookingPaymentDto = new CreateBookingPaymentDto(
            type: PaymentMethodType::card(),
            amount: $amount,
            demand: $demand,
            user: $this->getAuthUser()
        );

        $bookingPayment = $service->createBookingPayment($bookingPaymentDto);
        $url = $bookingPayment["formUrl"];
        $orderId = $bookingPayment["orderId"];

        $paidBookingPaymentTime = Carbon::now()->addMinutes(10);

        PayBookingTime::create([
            "crm_id" => $demand->getId(),
            "customer_id" => $this->getAuthUser()->crm_id,
            "end_date" => $demand->getEndDate(),
            "time_to_pay" => $paidBookingPaymentTime,
            "contract_id" => $contractId,
            "payment_url" => $url,
            "status" => "payment_await",
            "order_id" => $orderId,
            "contract_number" => $demand->getContract()?->getName(),
            "order_creation_time" => Carbon::now()->toDateTimeString(),
            "email" => $this->getAuthUser()->email,
            "register_do_log" => $bookingPayment["log"]
        ]);

        return $this->response(["url" => $url]);
        // phpcs:enable
    }
}
