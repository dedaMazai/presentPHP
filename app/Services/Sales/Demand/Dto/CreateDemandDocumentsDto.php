<?php

namespace App\Services\Sales\Demand\Dto;

/**
 * Class CreateDemandDocumentsDto
 *
 * @package App\Services\Sales\Demand\Dto
 */
class CreateDemandDocumentsDto
{
    /**
     * @param string              $demandId
     * @param DemandDocumentDto[] $documents
     */
    public function __construct(
        public string $demandId,
        public array $documents,
    ) {
    }
}
