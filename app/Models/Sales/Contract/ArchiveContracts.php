<?php

namespace App\Models\Sales\Contract;

/**
 * Class ArchiveContracts
 *
 * @package App\Models\Sale\Contract
 */
class ArchiveContracts
{
    public function __construct(
        private string $id,
        private string $contractName,
        private string $contractDate,
        private string $projectName,
        private string $nameLk,
        private int $number,
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
    public function getContractName(): string
    {
        return $this->contractName;
    }

    /**
     * @return string
     */
    public function getContractDate(): string
    {
        return $this->contractDate;
    }

    /**
     * @return string
     */
    public function getProjectName(): string
    {
        return $this->projectName;
    }

    /**
     * @return string
     */
    public function getNameLk(): string
    {
        return $this->nameLk;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }
}
