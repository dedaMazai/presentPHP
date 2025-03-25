<?php

namespace App\Http\Api\External\V1\Requests\Contract;

use App\Http\Api\External\V1\Requests\Request;

/**
 * Class GetAdditionalRequest
 *
 * @package App\Http\Api\External\V1\Requests\Contract
 */
class GetAdditionalRequest extends Request
{
    public function rules(): array
    {
        return [
            'type' => 'required',
        ];
    }
}
