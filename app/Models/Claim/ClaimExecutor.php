<?php

namespace App\Models\Claim;

/**
 * Class ClaimExecutor
 *
 * @package App\Models\Claim
 */
class ClaimExecutor
{
    public function __construct(
        private string $name,
        private ?string $jobTitle,
        private ?string $urlPhoto,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    public function getUrlPhoto(): ?string
    {
        return $this->urlPhoto;
    }
}
