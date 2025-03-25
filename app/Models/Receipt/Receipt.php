<?php

namespace App\Models\Receipt;

/**
 * Class Receipt
 *
 * @package App\Models\Receipt
 */
class Receipt
{
    public function __construct(
        private int $year,
        private int $month,
        private int $total,
        private ?string $pdf,
        private ReceiptStatus $status,
        private int $paid,
    ) {
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getMonth(): int
    {
        return $this->month;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getPdf(): ?string
    {
        return $this->pdf;
    }

    public function getStatus(): ReceiptStatus
    {
        return $this->status;
    }

    public function getPaid(): int
    {
        return $this->paid;
    }
}
