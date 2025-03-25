<?php

namespace App\Http\Admin\Requests;

/**
 * Class SaveMortgageProgramRequest
 *
 * @package App\Http\Admin\Requests
 */
class SaveMortgageProgramRequest extends Request
{
    public function rules(): array
    {
        return [
            'bank_info_id' => 'required|integer|exists:bank_info,id',
            'initial_payment' => 'required|numeric',
            'citizenship' => 'required|string',
            'period' => 'required|integer',
            'addresses' => 'nullable|string',
        ];
    }
}
