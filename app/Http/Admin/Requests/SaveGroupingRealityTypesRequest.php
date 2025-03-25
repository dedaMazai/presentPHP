<?php

namespace App\Http\Admin\Requests;

/**
 * Class SaveGroupingRealityTypesRequest
 *
 * @package App\Http\Admin\Requests
 */
class SaveGroupingRealityTypesRequest extends Request
{
    public function rules(): array
    {
        return [
            'group_reality_name' => 'required|string|max:255',
            'group_reality_ids' => 'required|array',
        ];
    }
}
