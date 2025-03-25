<?php

namespace App\Services\Sberbank;

use App\Models\TransactionLog\TransactionLog;
use App\Services\Payment\Dto\CreateBookingPaymentDto;
use App\Services\Payment\Dto\CreatePaymentDto;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use RuntimeException;

/**
 * Class SberbankClient
 *
 * @package App\Services\Sberbank
 */
class SberbankClient
{
    private HttpClient $httpClient;

    public function __construct(
        string $baseUri,
        private array $credentials,
        private string $salesSellerId,
        private readonly array $booking
    ) {
        $this->httpClient = new HttpClient([
            "base_uri" => $baseUri,
        ]);
    }

    public function createPayment(TransactionLog $transactionLog, CreatePaymentDto $dto): array
    {
        $items = [];

        foreach ($dto->items as $itemDto) {
            $items[] = [
                "positionId" => $itemDto->positionId,
                "name" => $itemDto->name,
                "quantity" => [
                    "value" => $itemDto->quantity,
                    "measure" => "штук",
                ],
                "itemCode" => $itemDto->itemCode,
                "itemPrice" => $itemDto->itemPrice,
            ];
        }

        $jsonParams = [
            "LastName" => $dto->lastName,
            "FirstName" => $dto->firstName,
            "MiddleName" => $dto->middleName,
        ];

        if ($dto->email) {
            $jsonParams["email"] = $dto->email;
        }

        return $this->request(
            "POST",
            "register.do",
            [
                RequestOptions::FORM_PARAMS => [
                    "userName" => $this->booking["booking_username"],
                    "password" => $this->booking["booking_password"],
                    "orderNumber" => "transaction-" . $transactionLog->id . "-" . time(),
                    "amount" => $dto->amount,
                    "merchantLogin" => $this->booking["booking_username"],
                    "returnUrl" => $dto->returnUrl,
                    "failUrl" => $dto->failUrl,
                    "orderBundle" => json_encode([
                        "cartItems" => [
                            "items" => $items,
                        ],
                    ]),
                    "jsonParams" => json_encode($jsonParams),
                ],
            ],
        );
    }

    public function createBookingPayment(CreateBookingPaymentDto $dto): array
    {
        $formParams = [
            "userName" => $this->booking["booking_username"],
            "password" => $this->booking["booking_password"],
            "email" => $dto->demand->getEmail(),
            'orderNumber' => 'deal-' . rand(100, 999) . '-' . time(),
            "amount" => $dto->amount * 100,
            "returnUrl" => url("/pay-booking/checkout-success"),
            "failUrl" => url(
                "/demands/" . $dto->demand->getId() . "/pay-booking/fail",
                [],
                true
            ),
            "orderBundle" => [
                "orderCreationDate" => Carbon::now()->format("Y-m-d\TH:i:s"),
                "ffdVersion" => "1.05",
                "receiptType" => "sell",
                "ismOptional" => false,
                "company" => [
                    "email" => "support@pioneer.ru",
                    "sno" => "osn",
                    "inn" => $this->booking["booking_inn"],
                    "paymentAddress" => "https://online.pioneer.ru"
                ],
                "cartItems" => [
                    "items" => [
                        [
                            "positionId" => "1",
                            "name" => $dto->demand->getPaidBookingContract()?->getName(),
                            "quantity" => [
                                "value" => 1,
                                "measure" => "штук",
                            ],
                            "itemCode" => "1",
                            "itemPrice" => $dto->amount * 100,
                            "itemAmount" => $dto->amount * 100,
                            "paymentMethod" => 'full_payment',
                            'paymentObject' => 'service',
                            "tax" => [
                                "taxType" => 6
                            ],
                        ],
                    ],
                ],
                "payments" => [
                    [
                        "type" => 1,
                        "sum" => $dto->amount * 100
                    ]
                ],
                "total" => $dto->amount * 100
            ],
            "jsonParams" => [
                "pdAgreement" => true,
                "LastName" => $dto->user->last_name,
                "FirstName" => $dto->user->first_name,
                "MiddleName" => $dto->user->middle_name,
                "contractNumber" => $dto->demand->getPaidBookingContract()?->getName(),
            ]
        ];

        $response = $this->request(
            "POST",
            $this->booking["booking_uri"] . "/ecomm/gw/partner/api/v1/register.do",
            [
                RequestOptions::JSON => $formParams,
            ],
        );

        $response["log"] = json_encode($formParams);

        return $response;
    }

    public function checkOrderStatus(string $orderId): array
    {
        return $this->request(
            "POST",
            $this->booking["booking_uri"] . "/ecomm/gw/partner/api/v1/getOrderStatusExtended.do",
            [
                RequestOptions::JSON => [
                    "userName" => $this->booking["booking_username"],
                    "password" => $this->booking["booking_password"],
                    "orderId" => $orderId
                ]
            ]
        );
    }

    public function getFiscalisation(string $orderId): array
    {
        return $this->request(
            "POST",
            $this->booking["booking_uri"] . "/ecomm/gw/partner/api/ofd/v1/getReceiptStatus",
            [
                RequestOptions::JSON => [
                    "userName" => $this->booking["booking_username"],
                    "password" => $this->booking["booking_password"],
                    "orderId" => $orderId
                ]
            ]
        );
    }

    public function retryReceipt(array $receiptIds): array
    {
        return $this->request(
            "POST",
            $this->booking["booking_uri"] . "/ecomm/gw/partner/api/ofd/v1/retryReceipt",
            [
                RequestOptions::FORM_PARAMS => [
                    "userName" => $this->booking["booking_username"],
                    "password" => $this->booking["booking_password"],
                    "receiptIds" => $receiptIds
                ]
            ]
        );
    }

    public function createPaymentByApplePay(
        TransactionLog $transactionLog,
        CreatePaymentDto $dto,
        string $tokenData
    ): array {
        $items = [];

        foreach ($dto->items as $itemDto) {
            $items[] = [
                "positionId" => $itemDto->positionId,
                "name" => $itemDto->name,
                "quantity" => [
                    "value" => $itemDto->quantity,
                    "measure" => "штук",
                ],
                "itemCode" => $itemDto->itemCode,
                "itemPrice" => $itemDto->itemPrice,
            ];
        }

        return $this->request(
            "POST",
            "payment.do",
            [
                RequestOptions::FORM_PARAMS => [
                    "orderNumber" => $transactionLog->id,
                    "merchant" => $this->booking["booking_username"],
                    "paymentToken" => base64_encode($tokenData),
                    "orderBundle" => json_encode([
                        "cartItems" => [
                            "items" => $items,
                        ],
                    ]),
                ],
            ],
        );
    }

    private function request(string $method, string $uri, array $options = []): array
    {
        try {
            $response = $this->httpClient->request($method, $uri, $options);

            return json_decode($response->getBody(), true);
        } catch (Exception | RequestException | GuzzleException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
