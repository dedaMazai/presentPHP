<?php

namespace App\Services\Sales;

use App\Models\Sales\Ownership;
use App\Models\Sales\TradeIn;

/**
 * Class TradeInRepository
 *
 * @package App\Services\Sales
 */
class TradeInRepository
{
    public function makeTradeIn(array $data): TradeIn
    {
        return new TradeIn(
            isTradeInAvailable: $data['isTradeInAvailable'],
            tradeInInfo: $data['tradeInInfo'],
        );
    }
}
