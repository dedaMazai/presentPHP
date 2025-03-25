<?php

namespace App\Http\Api\External\V1\Requests\Contract;

use App\Http\Api\External\V1\Requests\Request;

/**
 * Class DemandSummaryRequest
 *
 * @package App\Http\Api\External\V1\Requests\Contract
 */
class DemandSummaryRequest extends Request
{
    public function rules(): array
    {
        return [
            'type' => 'required|string',
            'id' => 'required|string',
        ];
    }
}
