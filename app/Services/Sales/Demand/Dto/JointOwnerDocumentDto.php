<?php

namespace App\Services\Sales\Demand\Dto;

use App\Models\Document\DocumentSubtype;
use App\Models\Document\DocumentType;

/**
 * Class JointOwnerDocumentDto
 *
 * @package App\Services\Sales\Demand\Dto
 */
class JointOwnerDocumentDto
{
    public function __construct(
        public string $name,
        public string $fileName,
        public DocumentType $type,
        public ?DocumentSubtype $subtype,
        public bool $isCustomerAvailable,
        public string $documentBody,
        public string $mimeType,
    ) {
    }
}
