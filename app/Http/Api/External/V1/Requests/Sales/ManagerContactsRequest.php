<?php

namespace App\Http\Api\External\V1\Requests\Sales;

use App\Http\Api\External\V1\Requests\Request;
use App\Models\Sales\ManagerObjectType;
use Illuminate\Validation\Rule;

/**
 * Class ManagerContactsRequest
 *
 * @package App\Http\Api\External\V1\Requests\Sales
 */
class ManagerContactsRequest extends Request
{
    public function rules(): array
    {
        return [
            'type' => ['required','string', Rule::in(ManagerObjectType::toValues())],
            'id' => ['required','string'],
        ];
    }
}
