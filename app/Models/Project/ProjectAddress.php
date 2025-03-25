<?php

namespace App\Models\Project;

use Carbon\Carbon;

/**
 * Class ProjectAddress
 *
 * @package App\Models\Project
 */
class ProjectAddress
{
    public function __construct(
        private string $id,
        private ?string $post,
        private ?string $prefixUK,
        private ?string $addressPostShort,
        private ?string $ukId,
        private ?string $ukName,
        private ?string $addressNumber,
        private ?int $floorCount,
        private ?int $sectionCount,
        private ?Carbon $dateExploitation,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPost(): ?string
    {
        return $this->post;
    }

    public function getPrefixUK(): ?string
    {
        return $this->prefixUK;
    }

    public function getAddressPostShort(): ?string
    {
        return $this->addressPostShort;
    }

    public function getUkId(): ?string
    {
        return $this->ukId;
    }

    public function getUkName(): ?string
    {
        return $this->ukName;
    }

    public function getAddressNumber(): ?string
    {
        return $this->addressNumber;
    }

    public function getFloorCount(): ?int
    {
        return $this->floorCount;
    }

    public function getSectionCount(): ?int
    {
        return $this->sectionCount;
    }

    public function getDateExploitation(): ?Carbon
    {
        return $this->dateExploitation;
    }
}
