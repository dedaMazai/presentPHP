<?php

namespace App\Http\Api\External\V1\Requests\Sales;

use App\Http\Api\External\V1\Requests\Request;

/**
 * Class CreateDemandRequest
 *
 * @package App\Http\Api\External\V1\Requests\Sales
 */
class CreateDemandRequest extends Request
{
    public function rules(): array
    {
        return [
            'property_id' => 'required|string|max:255',
        ];
    }
}
