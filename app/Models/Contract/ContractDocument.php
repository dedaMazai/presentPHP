<?php

namespace App\Models\Contract;

/**
 * Class ContractDocument
 *
 * @package App\Models\Contract
 */
class ContractDocument
{
    public function __construct(
        private string $id,
        private string $jointOwnerId,
        private string $fullName,
        private ?array $templates,
        private ?array $documents,
    ) {
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getJointOwnerId(): string
    {
        return $this->jointOwnerId;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->fullName;
    }

    /**
     * @return array|null
     */
    public function getTemplates(): ?array
    {
        return $this->templates;
    }

    /**
     * @return array|null
     */
    public function getDocuments(): ?array
    {
        return $this->documents;
    }
}
