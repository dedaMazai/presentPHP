<?php

namespace App\Services\Claim\Dto;

use App\Models\Document\DocumentSubtype;
use App\Models\Document\DocumentType;

/**
 * Class ClaimImageDto
 *
 * @package App\Services\Claim\Dto
 */
class ClaimImageDto
{
    public function __construct(
        public string $name,
        public string $fileName,
        public DocumentType $documentType,
        public ?DocumentSubtype $documentSubtype,
        public bool $isCustomerAvailable,
        public string $documentBody,
        public string $mimeType,
        public string $sender,
    ) {
    }
}
