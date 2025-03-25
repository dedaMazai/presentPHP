<?php

namespace App\Http\Api\External\V1\Requests\Claim;

/**
 * Class SaveClaimAppealRequest
 *
 * @package App\Http\Api\External\V1\Requests\Claim
 */
class SaveClaimAppealRequest extends ClaimManipulationRequest
{
    public function rules(): array
    {
        return [
            'claim_catalogue_item_ids' => 'required|array',
            'claim_catalogue_item_ids.*' => 'required|string',
            'comment' => 'required|string',
            'images' => 'array',
            'images.*' => 'file',
        ];
    }
}
