<?php

namespace App\Services\Sales\Demand\Dto;

use App\Models\Document\DocumentSubtype;
use App\Models\Document\DocumentType;

/**
 * Class DemandDocumentDto
 *
 * @package App\Services\Sales\Demand\Dto
 */
class DemandDocumentDto
{
    public function __construct(
        public string $name,
        public string $fileName,
        public DocumentType $type,
        public DocumentSubtype $subtype,
        public bool $isCustomerAvailable,
        public string $documentBody,
        public string $mimeType,
    ) {
    }
}
