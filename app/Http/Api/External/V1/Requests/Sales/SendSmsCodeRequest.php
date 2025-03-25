<?php

namespace App\Http\Api\External\V1\Requests\Sales;

use App\Http\Api\External\V1\Requests\Request;

/**
 * Class SendSmsCodeRequest
 *
 * @package App\Http\Api\External\V1\Requests\Sales
 */
class SendSmsCodeRequest extends Request
{
    public function rules(): array
    {
        return [
            'customer_id' => 'string',
            'contract_id' => 'required|string',
            'code' => 'required|integer',
        ];
    }
}
