<?php

namespace App\Models\Sales;

/**
 * Class TradeIn
 *
 * @package App\Models\Sales
 */
class TradeIn
{
    public function __construct(
        private bool $isTradeInAvailable,
        private ?string $tradeInInfo,
    ) {
    }

    public function isTradeInAvailable(): bool
    {
        return $this->isTradeInAvailable;
    }

    public function getTradeInInfo(): ?string
    {
        return $this->tradeInInfo;
    }
}
