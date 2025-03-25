<?php

namespace App\Services\Receipt;

use App\Models\Receipt\Receipt;
use App\Models\Receipt\ReceiptFile;
use App\Models\Receipt\ReceiptStatus;
use App\Services\Crm\CrmClient;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

/**
 * Class ReceiptRepository
 *
 * @package App\Services\Receipt
 */
class ReceiptRepository
{
    public function __construct(
        private CrmClient $crmClient,
        private DynamicsCrmClient $dynamicsCrmClient
    ) {
    }

    /**
     * @throws ValidationException
     */
    public function getReceipts(
        string $accountNumber,
        Carbon $startDate,
        Carbon $endDate,
    ): array {
        $data = $this->crmClient->getReceipts($accountNumber, $startDate, $endDate);

        return array_map(fn($data) => $this->makeReceipt($data), $data);
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function getReceiptPdfByUri(string $uri): ReceiptFile
    {
        $data = $this->dynamicsCrmClient->getDocumentByUri($uri);

        return $this->makeReceiptFile($data);
    }

    private function makeReceipt(array $data): Receipt
    {
        return new Receipt(
            $data['year'],
            $data['month'],
            $data['total'],
            $data['pdf'] ?? null,
            ReceiptStatus::from($data['status']),
            $data['paid'],
        );
    }

    private function makeReceiptFile(array $data): ReceiptFile
    {
        return new ReceiptFile(
            $data['name'],
            $data['fileName'],
            $data['documentBody'],
            $data['mimeType'],
        );
    }
}
