<?php

namespace App\Http\Api\External\V1\Requests\LegalPerson;

use App\Http\Api\External\V1\Requests\Request;

/**
 * Class UpdateLegalPesronRequest
 *
 * @package App\Http\Api\External\V1\Requests\LegalPerson
 */
class UpdateLegalPersonRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'string|nullable',
            'inn' => 'string|nullable',
            'ogrn' => 'string|nullable',
            'kpp' => 'string|nullable',
            'address_legal' => 'string|nullable',
            'phone' => 'string|nullable',
            'mail' => 'string|nullable',
        ];
    }
}
