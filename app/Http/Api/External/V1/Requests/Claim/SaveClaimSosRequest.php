<?php

namespace App\Http\Api\External\V1\Requests\Claim;

/**
 * Class SaveClaimSosRequest
 *
 * @package App\Http\Api\External\V1\Requests\Claim
 */
class SaveClaimSosRequest extends ClaimManipulationRequest
{
    public function rules(): array
    {
        return [
            'claim_catalogue_item_id' => 'required|string',
            'comment' => 'required|string',
            'images' => 'array',
            'images.*' => 'file',
        ];
    }
}
