<?php

namespace App\Http\Api\External\V1\Requests\Sales;

use App\Http\Api\External\V1\Requests\Request;

/**
 * Class SendContractDraftRequest
 *
 * @package App\Http\Api\External\V1\Requests\Sales
 */
class SendContractDraftRequest extends Request
{
    public function rules(): array
    {
        return [
            'customer_id' => 'string',
            'demand_id' => 'required|string',
        ];
    }
}
