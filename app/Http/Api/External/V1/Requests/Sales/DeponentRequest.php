<?php

namespace App\Http\Api\External\V1\Requests\Sales;

use App\Http\Api\External\V1\Requests\Request;
use App\Models\Sales\ManagerObjectType;
use Illuminate\Validation\Rule;

/**
 * Class DeponentRequest
 *
 * @package App\Http\Api\External\V1\Requests\Sales
 */
class DeponentRequest extends Request
{
    public function rules(): array
    {
        return [
            'type' => ['required','string', Rule::in(ManagerObjectType::toValues())],
            'id' => ['required','string'],
        ];
    }
}
