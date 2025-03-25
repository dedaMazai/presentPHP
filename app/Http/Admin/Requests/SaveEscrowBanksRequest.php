<?php

namespace App\Http\Admin\Requests;

/**
 * Class SaveEscrowBanksRequest
 *
 * @package App\Http\Admin\Requests
 */
class SaveEscrowBanksRequest extends Request
{
    public function rules(): array
    {
        return [
            'escrow_bank_id' => 'required|integer|exists:banks,id',
            'letterofbank_ids' => 'required|array',
        ];
    }
}
