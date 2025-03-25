<?php

namespace App\Models\Claim\ClaimCatalogue;

use App\Models\Claim\ClaimPriceType;
use App\Models\Claim\ClaimTheme;
use Illuminate\Support\Collection;

/**
 * Class ClaimCatalogueItem
 *
 * @package App\Models\Claim\ClaimCatalogue
 */
class ClaimCatalogueItem
{
    /**
     * @param string                              $id
     * @param string                              $name
     * @param string|null                         $parentId
     * @param ClaimTheme|null                     $theme
     * @param ClaimCatalogueItemGroup             $group
     * @param ClaimPriceType|null                 $priceType
     * @param int|null                            $price
     * @param bool                                $isPopular
     * @param bool                                $isService
     * @param Collection<ClaimCatalogueItem>      $children
     * @param ClaimCatalogueItemImages            $images
     * @param string|null                         $description
     * @param int|null                            $timeLocalization
     * @param int|null                            $timeReaction
     * @param int|null                            $timeSolution
     * @param array|null                          $unit
     * @param ClaimCatalogueItemSelectOption|null $selectOption
     * @param array|null                          $workSchedule
     * @param string                              $title
     * @param int|null                            $order
     * @param int|null                            $executionNorm
     * @param array|null                          $nds
     * @param array|null                          $accountServiceSeller
     * @param array|null                          $sellerTypeCode
     * @param bool                                $isDisplayedInLk
     * @param bool                                $isDisplay
     */
    public function __construct(
        private string $id,
        private string $name,
        private ?string $parentId,
        private ?ClaimTheme $theme,
        private ClaimCatalogueItemGroup $group,
        private ?ClaimPriceType $priceType,
        private ?int $price,
        private bool $isPopular,
        private bool $isService,
        private Collection $children,
        private ClaimCatalogueItemImages $images,
        private ?string $description,
        private ?int $timeLocalization,
        private ?int $timeReaction,
        private ?int $timeSolution,
        private ?array $unit,
        private ?ClaimCatalogueItemSelectOption $selectOption,
        private ?array $workSchedule,
        private string $title,
        private ?int $order,
        private ?int $executionNorm,
        private ?array $nds,
        private ?array $accountServiceSeller,
        private ?array $sellerTypeCode,
        private bool $isDisplayedInLk,
        private bool $isDisplay,
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

    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    public function getTheme(): ?ClaimTheme
    {
        return $this->theme;
    }

    public function getGroup(): ClaimCatalogueItemGroup
    {
        return $this->group;
    }

    public function getPriceType(): ?ClaimPriceType
    {
        return $this->priceType;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function getIsPopular(): bool
    {
        return $this->isPopular;
    }

    public function getIsService(): bool
    {
        return $this->isService;
    }

    /**
     * @return Collection<ClaimCatalogueItem>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function getImages(): ClaimCatalogueItemImages
    {
        return $this->images;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getTimeLocalization(): ?int
    {
        return $this->timeLocalization;
    }

    public function getTimeReaction(): ?int
    {
        return $this->timeReaction;
    }

    public function getTimeSolution(): ?int
    {
        return $this->timeSolution;
    }

    public function getUnit(): ?array
    {
        return $this->unit;
    }

    public function getSelectOption(): ?ClaimCatalogueItemSelectOption
    {
        return $this->selectOption;
    }

    public function getWorkSchedule(): ?array
    {
        return $this->workSchedule;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function getExecutionNorm(): ?int
    {
        return $this->executionNorm;
    }

    public function getNds(): ?array
    {
        return $this->nds;
    }

    public function getAccountServiceSeller(): ?array
    {
        return $this->accountServiceSeller;
    }

    public function getSellerTypeCode(): ?array
    {
        return $this->sellerTypeCode;
    }

    public function getIsDisplayedInLk(): bool
    {
        return $this->isDisplayedInLk;
    }

    public function getIsDisplay(): bool
    {
        return $this->isDisplay;
    }
}
