<?php

namespace App\Http\Api\External\V1\Requests\Claim;

/**
 * Class SaveClaimVisitRequest
 *
 * @package App\Http\Api\External\V1\Requests\Clait
 */
class SaveClaimVisitRequest extends ClaimManipulationRequest
{
    public function rules(): array
    {
        return [
            'claim_catalogue_item_id' => 'required|string',
            'arrival_date' => 'nullable|date',
            'comment' => 'nullable|string',
            'images' => 'array',
            'images.*' => 'file',
        ];
    }
}
