<?php

namespace App\Services\Claim\Dto;

use App\Models\Claim\ClaimCatalogue\ClaimCatalogueItem;
use Carbon\Carbon;

/**
 * Class SaveClaimVisitDto
 *
 * @package App\Services\Claim\Dto
 */
class SaveClaimAttachmentDto
{
    /**
     * @param ClaimCatalogueItem $claimCatalogueItem
     * @param Carbon|null        $arrivalDate
     * @param string|null        $comment
     * @param ClaimImageDto[]    $imageDtos
     */
    public function __construct(
        public string $file_name,
        public string  $file_body,
        public string $mime_type,
        public int $document_type_code,
        public string $document_type_name,
        public string $claim_id,
        public string $crm_user_id,
    ) {
    }
}
