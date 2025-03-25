<?php

namespace App\Http\Api\External\V1\Requests\Sales\Inspection;

use App\Http\Api\External\V1\Requests\Request;

/**
 * Class CreateInspectionRequest
 *
 * @package App\Http\Api\External\V1\Requests\Sales\Inspection
 */
class CreateInspectionRequest extends Request
{
    public function rules(): array
    {
        return [
            'room_id' => 'required|int',
            'date' => 'required|string',
            'time' => 'required|string',
        ];
    }
}
