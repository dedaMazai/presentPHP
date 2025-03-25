<?php

namespace App\Models\Sales\Bank;

/**
 * Class Bank
 *
 * @package App\Models\Sales
 */
class Bank
{
    public function __construct(
        private string $id,
        private string $name,
        private ?string $nameShort,
        private ?string $nameFull,
        private BankType $type,
        private ?BankInfo $appendInfo,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNameShort(): ?string
    {
        return $this->nameShort;
    }

    public function getNameFull(): ?string
    {
        return $this->nameFull;
    }

    public function getType(): BankType
    {
        return $this->type;
    }

    public function getAppendInfo(): ?BankInfo
    {
        return $this->appendInfo;
    }
}
