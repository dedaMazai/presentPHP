<?php

namespace App\Http\Api\External\V1\Requests;

use App\Models\TransactionLog\SberbankOperationType;
use Illuminate\Validation\Rule;

/**
 * Class PaymentCallbackRequest
 *
 * @property int|null $amount
 * @property string $mdOrder
 * @property string $orderNumber
 * @property string $checksum
 * @property string $operation
 * @property int $status
 * @package App\Http\Api\External\V1\Requests
 */
class PaymentCallbackRequest extends Request
{
    public function rules(): array
    {
        return [
            'amount' => 'integer',
            'mdOrder' => 'required|string',
            'orderNumber' => 'required|string',
            'checksum' => 'required|string',
            'operation' => [
                'required',
                Rule::in(SberbankOperationType::toValues()),
            ],
            'status' => 'required|integer',
        ];
    }
}
