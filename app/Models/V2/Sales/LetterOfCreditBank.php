<?php

namespace App\Models\V2\Sales;

/**
 * Class Customer
 *
 * @package App\Models\Sales
 */
class LetterOfCreditBank
{
    public function __construct(
        private string $id,
        private ?string $name,
        private ?string $image
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
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }
}
