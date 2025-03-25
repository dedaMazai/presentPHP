<?php

namespace App\Http\Api\External\V1\Requests\Claim;

/**
 * Class SaveClaimWarrantyRequest
 *
 * @package App\Http\Api\External\V1\Requests\Claim
 */
class SaveClaimWarrantyRequest extends ClaimManipulationRequest
{
    public function rules(): array
    {
        return [
            'claim_catalogue_item_ids' => 'required|array',
            'claim_catalogue_item_ids.*' => 'required|string',
            'arrival_date' => 'nullable|date',
            'comment' => 'required|string',
            'images' => 'array',
            'images.*' => 'file',
        ];
    }
}
