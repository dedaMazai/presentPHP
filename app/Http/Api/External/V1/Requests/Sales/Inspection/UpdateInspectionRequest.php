<?php

namespace App\Http\Api\External\V1\Requests\Sales\Inspection;

use App\Http\Api\External\V1\Requests\Request;

/**
 * Class UpdateInspectionRequest
 *
 * @package App\Http\Api\External\V1\Requests\Sales\Inspection
 */
class UpdateInspectionRequest extends Request
{
    public function rules(): array
    {
        return [
            'date' => 'required|string',
            'time' => 'required|string',
        ];
    }
}
