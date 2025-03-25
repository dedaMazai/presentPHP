<?php

namespace App\Http\Api\External\V1\Requests\Claim;

/**
 * Class SaveClaimMarketplaceRequest
 *
 * @package App\Http\Api\External\V1\Requests\Claim
 */
class SaveClaimAttachmentRequest extends ClaimManipulationRequest
{
    public function rules(): array
    {
        return [
            'file_name' => 'string|nullable',
            'file_body' => 'string|nullable',
            'mime_type' => 'string|nullable',
            'document_type_code' => 'int|nullable',
            'document_type_name' => 'string|nullable',
            'claim_id' => 'string|nullable',
            'crm_user_id' => 'string|nullable',
        ];
    }
}
