<?php

namespace App\Http\Api\External\V1\Requests\Sales;

use App\Http\Api\External\V1\Requests\Request;
use App\Models\Bank\BankType;
use App\Models\Sales\SaleDocumentType;
use Illuminate\Validation\Rule;

/**
 * Class CreateFileRequest
 *
 * @package App\Http\Api\External\V1\Requests\Sales
 */
class CreateFileRequest extends Request
{
    public function rules(): array
    {
        return [
            'customer_id' => ['string'],
            'object_id' => ['required','string'],
            'object_type' => ['required','int'],
            'files' => 'required|array',
            'files.*.name' => 'required|string',
            'files.*.body' => 'required|string',
            'files.*.mime_type' => 'required|string',
        ];
    }
}
