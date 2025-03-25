<?php

namespace App\Http\Api\External\V1\Requests\Claim;

/**
 * Class SaveClaimMarketplaceRequest
 *
 * @package App\Http\Api\External\V1\Requests\Claim
 */
class SaveClaimMarketplaceRequest extends ClaimManipulationRequest
{
    public function rules(): array
    {
        return [
            'claim_catalogue_items' => 'required|array',
            'claim_catalogue_items.*.id' => 'required|string',
            'claim_catalogue_items.*.count' => 'required|numeric',
            'arrival_date' => 'nullable|date',
            'images' => 'array',
            'images.*' => 'file',
        ];
    }
}
