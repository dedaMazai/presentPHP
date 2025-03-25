<?php

namespace App\Http\Admin\Requests;

use App\Models\Sales\Bank\BankType;
use Illuminate\Validation\Rule;

/**
 * Class SaveBankInfoRequest
 *
 * @package App\Http\Admin\Requests
 */
class SaveBankInfoRequest extends Request
{
    public function rules(): array
    {
        return [
            'is_published' => 'required|boolean',
            'title' => 'required|string|max:255',
            'logo_image_id' => 'required|integer|exists:images,id',
            'price' => 'required|numeric',
            'link' => 'string|nullable',
            'crm_id' => 'required|string',
            'type' => [
                'required',
                Rule::in(BankType::toValues()),
            ],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'type' => BankType::from($this->type),
        ]);
    }
}
