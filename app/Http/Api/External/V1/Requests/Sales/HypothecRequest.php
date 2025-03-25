<?php

namespace App\Http\Api\External\V1\Requests\Sales;

use App\Http\Api\External\V1\Requests\Request;
use App\Models\Bank\BankType;
use App\Models\Sales\SaleDocumentType;
use Illuminate\Validation\Rule;

/**
 * Class HypothecRequest
 *
 * @package App\Http\Api\External\V1\Requests\Sales
 */
class HypothecRequest extends Request
{
    public function rules(): array
    {
        return [
            'dh_is_integration' => ['required','boolean'],
            'bank_name' => ['string'],
            'manager' => ['string'],
            'date_approval' => ['string'],
            'phone_manager' => ['string'],
            'email_manager' => ['string'],
        ];
    }
}
