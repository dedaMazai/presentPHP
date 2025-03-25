<?php

namespace App\Http\Api\External\V1\Requests\Sales;

use App\Http\Api\External\V1\Requests\Request;
use App\Models\Bank\BankType;
use Illuminate\Validation\Rule;

/**
 * Class CreateDemandRequest
 *
 * @package App\Http\Api\External\V1\Requests\Sales
 */
class TradeInRequest extends Request
{
    public function rules(): array
    {
        return [
            'customer_id' => ['string'],
            'demand_id' => ['required','string'],
        ];
    }
}
