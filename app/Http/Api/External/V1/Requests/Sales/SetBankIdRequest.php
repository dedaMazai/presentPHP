<?php

namespace App\Http\Api\External\V1\Requests\Sales;

use App\Http\Api\External\V1\Requests\Request;
use App\Models\Sales\Bank\DemandBankType;
use Illuminate\Validation\Rule;

/**
 * Class CreateDemandRequest
 *
 * @package App\Http\Api\External\V1\Requests\Sales
 */
class SetBankIdRequest extends Request
{
    public function rules(): array
    {
        return [
            'bank_id' => ['required','string'],
            'bank_type' => ['required','string'],
            'is_sber_client' => ['bool']
        ];
    }
}
