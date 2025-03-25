<?php

namespace App\Models\Account;

/**
 * Class AccountDocument
 *
 * @package App\Models\Account
 */
class AccountTheme
{
    public function __construct(
        private string $theme_id,
        private string $name,
        private ?string $description,
        private ?string $market_image_url = null,
    ) {
    }

    public function getThemeId(): string
    {
        return $this->theme_id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getMarketImageUrl(): ?string
    {
        return $this->market_image_url;
    }

    public function setMarketImageUrl(?string $market_image_url): void
    {
        $this->market_image_url = $market_image_url;
    }
}
