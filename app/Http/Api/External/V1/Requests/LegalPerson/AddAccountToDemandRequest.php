<?php

namespace App\Http\Api\External\V1\Requests\LegalPerson;

use App\Http\Api\External\V1\Requests\Request;

/**
 * Class AddAccountToDemandRequest
 *
 * @package App\Http\Api\External\V1\Requests
 */
class AddAccountToDemandRequest extends Request
{
    public function rules(): array
    {
        return [
            'account_type' => 'string|required',
            'name' => 'string|required',
            'inn' => 'string|required',
            'ogrn' => 'string|required',
            'kpp' => 'string',
            'address_legal' => 'string|required',
            'phone' => 'string',
            'mail' => 'string',
        ];
    }
}
