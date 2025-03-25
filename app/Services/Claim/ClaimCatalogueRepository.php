<?php

namespace App\Services\Claim;

use App\Models\Claim\ClaimCatalogue\ClaimCatalogueItem;
use App\Models\Claim\ClaimCatalogue\ClaimCatalogueItemGroup;
use App\Models\Claim\ClaimCatalogue\ClaimCatalogueItemImages;
use App\Models\Claim\ClaimCatalogue\ClaimCatalogueItemSelectOption;
use App\Models\Claim\ClaimCatalogue\ClaimCatalogueTheme;
use App\Models\Claim\ClaimPriceType;
use App\Models\Claim\ClaimTheme;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\SettingsService;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use RuntimeException;

/**
 * Class ClaimCatalogueRepository
 *
 * @package App\Services\Claim
 */
class ClaimCatalogueRepository
{
    public function __construct(
        private DynamicsCrmClient $client,
        private CacheInterface $cache,
        private SettingsService $settingsService,
    ) {
    }

    /**
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    public function getAllByTheme(ClaimTheme $theme): ClaimCatalogueTheme
    {
        $tree = $this->cache->get($this->keyTree());
        if ($tree === null) {
            $tree = $this->reloadTreeCache();
        }

        return $tree[$theme->value] ?? new ClaimCatalogueTheme($theme->value, new Collection());
    }

    /**
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    public function getAllAccountByTheme(ClaimTheme $theme, string $accountNumber): ClaimCatalogueTheme
    {
        $tree = $this->cache->get($this->keyAccountTree($accountNumber));

        if ($tree === null) {
            $tree = $this->reloadAccountTreeCache(accountNumber: $accountNumber);
        }

        return $tree[$theme->value] ?? new ClaimCatalogueTheme($theme->value, new Collection());
    }

    /**
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    public function getSearchResults(ClaimTheme $theme, string $query): array
    {
        $categories = new Collection();
        $services = new Collection();

        $rawData = $this->getRawData();
        foreach ($rawData as $rawItem) {
            if (!isset($rawItem['serviceCatalogParent']['code'])) {
                continue;
            }
            if (!isset($rawItem['incidentClassificationCode']['code'])
                || $rawItem['incidentClassificationCode']['code'] != $theme->value
            ) {
                continue;
            }
            if (!isset($rawItem['isDisplayedInLk']) || !$rawItem['isDisplayedInLk']) {
                continue;
            }

            if (preg_match('#(' . $query . ')#iu', $rawItem['heading'] ?? '') !== 0
                || preg_match('#(' . $query . ')#iu', $rawItem['name'] ?? '') !== 0
                || preg_match('#(' . $query . ')#iu', $rawItem['description'] ?? '') !== 0
            ) {
                $group = ClaimCatalogueItemGroup::from($rawItem['groupingCode']['code']);

                if ($group->equals(ClaimCatalogueItemGroup::service())) {
                    $services->add($this->makeClaimCatalogueItem($rawItem));
                } else {
                    $categories->add($this->makeClaimCatalogueItem($rawItem));
                }
            }
        }

        return [
            'categories' => $categories,
            'services' => $services,
        ];
    }

    /**
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    public function getAccountSearchResults(ClaimTheme $theme, string $query, $accountNumber): array
    {
        $categories = new Collection();
        $services = new Collection();

        $rawData = $this->getAccountRawData(accountNumber: $accountNumber);
        foreach ($rawData as $rawItem) {
            if (!isset($rawItem['serviceCatalogParent']['code'])) {
                continue;
            }
            if (!isset($rawItem['incidentClassificationCode']['code'])
                || $rawItem['incidentClassificationCode']['code'] != $theme->value
            ) {
                continue;
            }
            if (!isset($rawItem['isDisplayedInLk']) || !$rawItem['isDisplayedInLk']) {
                continue;
            }

            if (preg_match('#(' . $query . ')#iu', $rawItem['heading'] ?? '') !== 0
                || preg_match('#(' . $query . ')#iu', $rawItem['name'] ?? '') !== 0
                || preg_match('#(' . $query . ')#iu', $rawItem['description'] ?? '') !== 0
            ) {
                $group = ClaimCatalogueItemGroup::from($rawItem['groupingCode']['code']);

                if ($group->equals(ClaimCatalogueItemGroup::service())) {
                    $services->add($this->makeClaimCatalogueItem($rawItem));
                } else {
                    $categories->add($this->makeClaimCatalogueItem($rawItem));
                }
            }
        }

        return [
            'categories' => $categories,
            'services' => $services,
        ];
    }

    /**
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    public function getOneById(string $id, int $amountKop = null): ?ClaimCatalogueItem
    {
        $rawData = $this->getRawData();

        foreach ($rawData as $rawItem) {
            if ($rawItem['id'] == $id) {
                if (($rawItem['paymentOptionCode']['code'] ?? null) === '3') {
                    $rawItem['priceKop'] = $amountKop;
                }

                $rawItem['children'] = $this->makeTreeChildren($rawData, [$rawItem['id']]);

                return $this->makeClaimCatalogueItem($rawItem);
            }
        }

        return null;
    }

    /**
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    public function getAll()
    {
        $rawData = $this->getRawData();

        return $rawData;
    }

    /**
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    public function getAccountOneById(string $accountNumber, string $id): ?ClaimCatalogueItem
    {
        $rawData = $this->getAccountRawData($accountNumber);

        foreach ($rawData as $rawItem) {
            if ($rawItem['id'] == $id) {
                $rawItem['children'] = $this->makeTreeChildren($rawData, [$rawItem['id']]);

                return $this->makeClaimCatalogueItem($rawItem);
            }
        }

        return null;
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws InvalidArgumentException
     */
    public function getPopularServicesByTheme(ClaimTheme $theme): Collection
    {
        //        $popularServices = $this->cache->get($this->keyPopularServices());
        //        if ($popularServices === null) {
        $popularServices = $this->reloadPopularServicesCache();

        //        }

        return $popularServices[$theme->value] ?? new Collection();
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws InvalidArgumentException
     */
    public function getAccountPopularServicesByTheme(string $accountNumber, ClaimTheme $theme): Collection
    {
        $popularServices = $this->reloadAccountPopularServicesCache($accountNumber);

        return $popularServices[$theme->value] ?? new Collection();
    }

    public function makeClaimCatalogueItem(array $item): ClaimCatalogueItem
    {
        $carousel = [];
        for ($i = 5; $i < 11; $i++) {
            if (isset($item["imageUrl0$i"])) {
                $carousel[] = $item["imageUrl0$i"];
            }
        }
        $images = new ClaimCatalogueItemImages(
            mainLarge: $item['imageUrl01'] ?? null,
            mainMiddle: $item['imageUrl02'] ?? null,
            mainSmall: $item['imageUrl03'] ?? null,
            catalogue: $item['imageUrl04'] ?? null,
            carousel: $carousel,
        );

        $group = ClaimCatalogueItemGroup::from($item['groupingCode']['code']);

        return new ClaimCatalogueItem(
            id: $item['id'],
            name: $item['name'],
            parentId: $item['serviceCatalogParent']['code'] ?? null,
            theme: ClaimTheme::tryFrom($item['incidentClassificationCode']['code'] ?? ''),
            group: $group,
            priceType: ClaimPriceType::tryFrom($item['paymentOptionCode']['code'] ?? ''),
            price: $item['priceKop'] ?? null,
            isPopular: $item['isPopularService'] ?? false,
            isService: $item['groupingCode']['code'] == 30,
            children: $item['children'] ?? new Collection(),
            images: $images,
            description: $item['description'] ?? null,
            timeLocalization: $item['timeLocalization'] ?? null,
            timeReaction: $item['timeReaction'] ?? null,
            timeSolution: $item['timeSolution'] ?? null,
            unit: $item['unitCode'] ?? null,
            selectOption: ClaimCatalogueItemSelectOption::tryFrom($item['selectionOptionCode']['code'] ?? ''),
            workSchedule: $item['workScheduleCode'] ?? null,
            title: $item['heading'] ?? $item['name'],
            order: $item['order'] ?? null,
            executionNorm: $item['executionNorm'] ?? null,
            nds: $item['nds'] ?? null,
            accountServiceSeller: $item['accountServiceSeller'] ?? null,
            sellerTypeCode: $item['sellerTypeCode'] ?? null,
            isDisplayedInLk: $item['isDisplayedInLk'] ?? false,
            isDisplay: $item['isDisplay'] ?? true,
        );
    }

    /**
     * @return ClaimCatalogueTheme[]
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    public function reloadTreeCache(): array
    {
        $rawData = $this->getRawData(resetCache: true);
        $tree = $this->makeTreeByThemes($rawData);

        $this->cacheTree($tree);

        return $tree;
    }

    /**
     * @return ClaimCatalogueTheme[]
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    public function reloadAccountTreeCache(string $accountNumber): array
    {
        $rawData = $this->getAccountRawData(accountNumber: $accountNumber, resetCache: true);
        $tree = $this->makeTreeByThemes($rawData);

        $this->cacheAccountTree($tree, $accountNumber);

        return $tree;
    }

    /**
     * @return array<Collection>
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    public function reloadPopularServicesCache(): array
    {
        $rawData = $this->getRawData();

        $popularServices = [];

        foreach ($rawData as $rawItem) {
            if (!isset($rawItem['incidentClassificationCode']['code']) || !isset($rawItem['groupingCode']['code'])) {
                continue;
            }

            $rawItemTheme = ClaimTheme::tryFrom($rawItem['incidentClassificationCode']['code'] ?? '');

            if (!$rawItemTheme) {
                continue;
            }

//            $rawItemGroup = ClaimCatalogueItemGroup::from($rawItem['groupingCode']['code']);

            if ($rawItem['isPopularService']
//                && $rawItemGroup->equals(ClaimCatalogueItemGroup::service())
            ) {
                if (!isset($popularServices[$rawItemTheme->value]) ||
                    !($popularServices[$rawItemTheme->value] instanceof Collection)
                ) {
                    $popularServices[$rawItemTheme->value] = new Collection();
                }
                $popularServices[$rawItemTheme->value]->add($this->makeClaimCatalogueItem($rawItem));
            }
        }

        foreach ($popularServices as $theme => $popularServiceTheme) {
            $popularServices[$theme] = $popularServiceTheme->sortBy(function (ClaimCatalogueItem $item) {
                return $item->getOrder();
            });
        }

        $this->cachePopularServices($popularServices);

        return $popularServices;
    }

    /**
     * @return array<Collection>
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    public function reloadAccountPopularServicesCache(string $accountNumber): array
    {
        $rawData = $this->getAccountRawData($accountNumber);

        $popularServices = [];

        foreach ($rawData as $rawItem) {
            if (!isset($rawItem['incidentClassificationCode']['code']) || !isset($rawItem['groupingCode']['code'])) {
                continue;
            }

            $rawItemTheme = ClaimTheme::tryFrom($rawItem['incidentClassificationCode']['code'] ?? '');

            if (!$rawItemTheme) {
                continue;
            }

//            $rawItemGroup = ClaimCatalogueItemGroup::from($rawItem['groupingCode']['code']);

            if ($rawItem['isPopularService']
//                && $rawItemGroup->equals(ClaimCatalogueItemGroup::service())
            ) {
                if (!isset($popularServices[$rawItemTheme->value]) ||
                    !($popularServices[$rawItemTheme->value] instanceof Collection)
                ) {
                    $popularServices[$rawItemTheme->value] = new Collection();
                }
                $popularServices[$rawItemTheme->value]->add($this->makeClaimCatalogueItem($rawItem));
            }
        }

        foreach ($popularServices as $theme => $popularServiceTheme) {
            $popularServices[$theme] = $popularServiceTheme->sortBy(function (ClaimCatalogueItem $item) {
                return $item->getOrder();
            });
        }

        $this->cacheAccountPopularServices($accountNumber, $popularServices);

        return $popularServices;
    }

    /**
     * @param  array  $data
     *
     * @return ClaimCatalogueTheme[]
     */
    private function makeTreeByThemes(array $data): array
    {
        // phpcs:disable
        $themesData = [];
        foreach (ClaimTheme::cases() as $theme) {
            $themesData[$theme->value] = new ClaimCatalogueTheme(
                $theme->value,
                $this->makeTreeChildren($data, [$this->settingsService->getClaimRootCategoryCrmId()], $theme),
            );

            if (isset($themesData[$theme->value]->getItems()[0])) {
                $themesData[$theme->value]->setItems($themesData[$theme->value]->getItems()[0]->getChildren());
            }
        }

        return $themesData;
    }

    /**
     * @param  array  $data
     * @param  string|null  $parentId
     * @param  ClaimTheme|null  $theme
     *
     * @return Collection<ClaimCatalogueItem>
     */
    private function makeTreeChildren(array $data, array $parentId = [], ?ClaimTheme $theme = null): Collection
    {
        $children = new Collection();

        foreach ($data as $item) {
            if (!isset($item['serviceCatalogParent']['code'])) {
                continue;
            }
            if ($theme
                && isset($item['incidentClassificationCode']['code'])
                && $item['incidentClassificationCode']['code'] != $theme->value
            ) {
                continue;
            }
            if (!isset($item['isDisplayedInLk']) || !$item['isDisplayedInLk']) {
                continue;
            }

            if (in_array($item['serviceCatalogParent']['code'], $parentId)) {
                $item['children'] = $this->makeTreeChildren($data, [$item['id']], $theme);

                $children->add($this->makeClaimCatalogueItem($item));
            }
        }

        return $children->sortBy(function (ClaimCatalogueItem $item) {
            return $item->getOrder();
        });
    }

    /**
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    protected function getRawData(bool $resetCache = false): array
    {
        if (!$resetCache) {
            $rawData = $this->cache->get($this->keyRawData());
            if ($rawData !== null) {
                return $rawData;
            }
        }

        $rawData = $this->client->getClaimCatalogue()['serviceUkList'] ?? throw new RuntimeException();
        $this->cacheRawData($rawData);
        return $rawData;
    }

    /**
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    protected function getAccountRawData(string $accountNumber, bool $resetCache = false): array
    {
        if (!$resetCache) {
            $rawData = $this->cache->get($this->keyAccountRawData($accountNumber));
            if ($rawData !== null) {
                return $rawData;
            }
        }

        $rawData = $this->client->getAccountCatalogue($accountNumber)['serviceUkList'] ?? throw new RuntimeException();

        $this->cacheAccountRawData($accountNumber, $rawData);
        return $rawData;
    }

    private function keyRawData(): string
    {
        return 'claims.catalogue.raw';
    }

    private function keyAccountRawData($number): string
    {
        return 'claims.'.$number.'.catalogue.raw';
    }

    private function keyTree(): string
    {
        return 'claims.catalogue.tree';
    }

    private function keyAccountTree(string $number): string
    {
        return 'accounts.'.$number.'.catalogue.tree';
    }

    private function keyPopularServices(): string
    {
        return 'claims.catalogue.popular';
    }

    private function keyAccountPopularServices($number): string
    {
        return 'accounts.'.$number.'.catalogue.popular';
    }

    /**
     * @throws InvalidArgumentException
     */
    private function cacheRawData(array $rawData): void
    {
        $this->cache->set($this->keyRawData(), $rawData, now()->addDay());
    }

    /**
     * @throws InvalidArgumentException
     */
    private function cacheAccountRawData(string $accountNumber, array $rawData): void
    {
        $this->cache->set($this->keyAccountRawData($accountNumber), $rawData, now()->addDay());
    }

    /**
     * @throws InvalidArgumentException
     */
    private function cacheTree(array $tree): void
    {
        $this->cache->set($this->keyTree(), $tree, now()->addDay());
    }

    /**
     * @throws InvalidArgumentException
     */
    private function cacheAccountTree(array $tree, string $accountNumber): void
    {
        $this->cache->set($this->keyAccountTree($accountNumber), $tree, now()->addDay());
    }

    /**
     * @throws InvalidArgumentException
     */
    private function cachePopularServices(array $popularServices): void
    {
        $this->cache->set($this->keyPopularServices(), $popularServices, now()->addDay());
    }

    /**
     * @throws InvalidArgumentException
     */
    private function cacheAccountPopularServices(string $accountNumber, array $popularServices): void
    {
        $this->cache->set($this->keyAccountPopularServices($accountNumber), $popularServices, now()->addDay());
    }
}
