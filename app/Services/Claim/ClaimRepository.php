<?php

namespace App\Services\Claim;

use App\Events\NewLastClaim;
use App\Http\Api\External\V1\Controllers\ClaimController;
use App\Jobs\MakeClaims;
use App\Models\Claim\Claim;
use App\Models\Claim\ClaimCatalogue\ClaimCatalogueItem;
use App\Models\Claim\ClaimExecutor;
use App\Models\Claim\ClaimFilter\ClaimFilterStatus;
use App\Models\Claim\ClaimImage;
use App\Models\Claim\ClaimImageContent;
use App\Models\Claim\ClaimPass\ClaimPassCar;
use App\Models\Claim\ClaimPass\ClaimPassCarType;
use App\Models\Claim\ClaimPass\ClaimPassHuman;
use App\Models\Claim\ClaimPass\ClaimPassStatus;
use App\Models\Claim\ClaimPass\ClaimPassType;
use App\Models\Claim\ClaimPaymentStatus;
use App\Models\Claim\ClaimService;
use App\Models\Claim\ClaimStatus;
use App\Models\Claim\ClaimTheme;
use App\Models\Document\DocumentType;
use App\Models\Relationship\Relationship;
use App\Models\User\User;
use App\Notifications\SendTelegramNotification;
use App\Services\Account\AccountRepository;
use App\Services\Contract\ContractRepository;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\ValidationException;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use ReflectionProperty;
use RuntimeException;
use Throwable;

/**
 * Class ClaimRepository
 *
 * @package App\Services\Claim
 */
class ClaimRepository
{
    public function __construct(
        private readonly DynamicsCrmClient        $dynamicsCrmClient,
        private readonly ClaimCatalogueRepository $catalogueRepository,
        private readonly CacheInterface           $cache,
        private readonly ContractRepository       $contractRepository,
        private readonly MakeClaim $makeClaim,
    ) {
    }

    /**
     * @param string $accountNumber
     * @param User $user
     * @param ClaimTheme|null $theme
     * @param Carbon|null $dateFrom
     * @param Carbon|null $dateTo
     * @param ClaimFilterStatus[] $filterStatuses
     * @param int|null $claimNumber
     * @param bool|null $isNotReadSms
     * @param bool|null $isNotReadDocument
     * @return Collection
     * @throws BadRequestException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function getAll(
        string $accountNumber,
        User $user,
        ?ClaimTheme $theme = null,
        ?Carbon $dateFrom = null,
        ?Carbon $dateTo = null,
        array $filterStatuses = [],
        int $claimNumber = null,
        bool $isNotReadSms = null,
        bool $isNotReadDocument = null,
    ): Collection {
        $claims = $this->getActualClaimsByAccountNumber($accountNumber);

        $filteredClaims = new Collection();
        $lastClaim = null;

        $contracts = $this->contractRepository->getContracts($user->crm_id);
        $thisContract = [];

        foreach ($contracts as $contract) {
            if ($contract->getPersonalAccount() === $accountNumber) {
                $thisContract = $contract;
                break;
            }
        }

        $jointOwners = $thisContract->getJointOwners();
        $thisJointOwner = [];

        foreach ($jointOwners as $jointOwner) {
            if ($jointOwner->getContactId() === $user->crm_id) {
                $thisJointOwner = $jointOwner;
                break;
            }
        }

        $role = !empty($thisJointOwner) ? $thisJointOwner->getRole()->value : "";

        foreach ($claims as $claim) {
            if (($role === "6" || $role === "7") && $claim->getDeclarantId() !== $user->crm_id) {
                continue;
            }

            if (!$lastClaim || $claim->getCreatedAt()->greaterThan($lastClaim->getCreatedAt())) {
                $lastClaim = $claim;
            }

            if ($theme && !$claim->getTheme()?->equals($theme)) {
                continue;
            }

            if ($claimNumber && $claim->getClaimNumber() === $claimNumber) {
                continue;
            }

            if ($isNotReadSms && $claim->getIsNotReadSms() === $isNotReadSms) {
                continue;
            }

            if ($isNotReadDocument && $claim->getIsNotReadDocument() === $isNotReadDocument) {
                continue;
            }

            if ($dateFrom && $claim->getCreatedAt()->lessThanOrEqualTo($dateFrom)) {
                continue;
            }

            if ($dateTo && $claim->getCreatedAt()->greaterThanOrEqualTo($dateTo)) {
                continue;
            }

            if ($filterStatuses && !in_array($claim->getStatus()->getFilterStatus(), $filterStatuses)) {
                if (in_array(ClaimFilterStatus::awaitingPayment(), $filterStatuses)) {
                    if (!$claim->getPaymentStatus() || $claim->getPaymentStatus()?->isFullyPaid()) {
                        continue;
                    }
                } else {
                    continue;
                }
            }
            $filteredClaims->add($claim);
        }

        if ($lastClaim) {
            $lastClaimRaw = [
                    'id' => $lastClaim->getId(),
                    'createdOn' => Carbon::parse($lastClaim->getCreatedAt())
                        ->shiftTimezone(6)
                        ->setTimezone(3) // restore timezone to origin from CRM
                        ->toIso8601String(),
            ];

            NewLastClaim::dispatch($accountNumber, $lastClaimRaw);
        }

        return $filteredClaims->sortByDesc(function ($item) {
            return $item->getCreatedAt();
        });
    }

    public function getAllByLastCreated(
        string $accountNumber,
    ): Collection {

        $claims = $this->getActualLastClaimByAccountNumber($accountNumber);

        $filteredClaims = new Collection();

        foreach ($claims as $claim) {
            $filteredClaims->add($claim);
        }

        return $filteredClaims->push($filteredClaims->sortByDesc(function ($item) {
            return $item->getCreatedAt();
        })->first());
    }

    public function getOneById(string $id): ?Claim
    {
        try {
            $data = $this->dynamicsCrmClient->getClaimById($id);

            return $this->makeClaim($data, withDetails: true);
        } catch (BadRequestException|NotFoundException|RuntimeException) {
            return null;
        }
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function getClaimImagesByClaimId(string $claimId): array
    {
        $data = $this->dynamicsCrmClient->getDocumentsByClaimId($claimId);

        return array_map(fn($data) => $this->makeClaimImage($data), $data['documentList'] ?? []);
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function getClaimImageContentByUri(string $uri): ClaimImageContent
    {
        $data = $this->dynamicsCrmClient->getDocumentByUri($uri);

        return $this->makeClaimImageContent($data);
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws InvalidArgumentException
     */
    private function makeClaim(array $data, bool $withDetails): Claim
    {
        $passCar = null;
        if (isset($data['car'])) {
            $passCar = new ClaimPassCar(
                carType: ClaimPassCarType::from($data['car']['carTypeCode']['code']),
                number: $data['car']['number'],
            );
        }

        $passHuman = null;
        if (isset($data['human'])) {
            $passHuman = new ClaimPassHuman(
                fullName: $data['human']['fullNameGuest'],
            );
        }

        $services = [];
        if (isset($data['services'])) {
            foreach ($data['services'] as $service) {
                if (!isset($service['serviceCatalogUkId'])) {
                    continue;
                }

                // phpcs:disable
                $catalogueItem = $this->catalogueRepository->getOneById($service['serviceCatalogUkId'], $service['costKop']);
                $catalogueItemParent = $this->getCatalogueItemTopParent($catalogueItem?->getParentId());
                // phpcs:enable

                $services[] = new ClaimService(
                    id: $service['id'],
                    catalogueItem: $catalogueItem,
                    catalogueItemParentId: $catalogueItemParent?->getId(),
                    catalogueItemParentName: $catalogueItemParent?->getTitle(),
                    amount: $service['amountKop'] ?? null,
                    cost: $service['costKop'] ?? null,
                    quantity: $service['quantity'] ?? null,
                    orderNumber: $service['orderNumber'] ?? null,
                );
            }
        }

        $user = null;
        $executors = [];
        $images = [];
        if ($withDetails) {
            if (isset($data['declarantId'])) {
                $user = User::firstWhere('crm_id', $data['declarantId']);
            }

            if (isset($data['executor'])) {
                $executors[] = new ClaimExecutor(
                    name: $data['executor']['name'],
                    jobTitle: $data['executor']['jobTitle'] ?? null,
                    urlPhoto: $data['executor']['urlPhoto'] ?? null,
                );
            }

            if (isset($data['executor2'])) {
                $executors[] = new ClaimExecutor(
                    name: $data['executor2']['name'],
                    jobTitle: $data['executor2']['jobTitle'] ?? null,
                    urlPhoto: $data['executor2']['urlPhoto'] ?? null,
                );
            }

            $images = $this->getClaimImagesByClaimId($data['id']);
        }

        $scheduledEnd = null;
        if ($data['status']['code'] != '1' && $data['status']['code'] != '100000000') {
            $scheduledEnd = $this->timezoneCorrection($data['scheduledEnd'] ?? null);
        }

        $code = $data['invoiceStatus']['code'] ?? '';
        if ($code === '7') {
            $code = '1';
        }

        return new Claim(
            id: $data['id'],
            number: $data['claimNumber'] ?? null,
            theme: ClaimTheme::from($data['incidentClassificationCode']['code'] ?? '') ?? null,
            status: ClaimStatus::from($data['status']['code']),
            createdAt: $this->timezoneCorrection($data['createdOn'] ?? null),
            closedAt: $this->timezoneCorrection($data['closedOn'] ?? null),
            paymentStatus: ClaimPaymentStatus::tryFrom($code),
            comment: $data['description'] ?? null,
            arrivalDate: $this->timezoneArrivalCorrection($data['arrivalDate'] ?? null),
            paymentDate: $this->timezoneCorrection($data['paymentDate'] ?? null),
            totalPayment: $data['totalPaymentKop'] ?? null,
            scheduledStart: $this->timezoneCorrection($data['scheduledStart'] ?? null),
            scheduledEnd: $scheduledEnd,
            user: $user,
            executors: $executors,
            passType: ClaimPassType::tryFrom($data['passTypeCode']['code'] ?? ''),
            passCar: $passCar,
            passHuman: $passHuman,
            passStatus: ClaimPassStatus::tryFrom($data['passStatus']['code'] ?? ''),
            services: $services,
            images: $images,
            confirmationCode: $data['confirmationCode'] ?? null,
            commentQuality: $data['commentQuality'] ?? null,
            rating: $data['qualityCode']['code'] ?? null,
            vendorId: $data['vendor']['code'] ?? null,
            vendorName: $data['vendor']['name'] ?? null,
            accountNumber: $data['personalAccount'] ?? null,
            declarantId: $data['declarantId'] ?? null,
            modifiedOn: $this->timezoneCorrection($data['modifiedOn'] ?? null),
            closedOn: $this->timezoneCorrection($data['closedOn'] ?? null),
            accountUkId: $data['accountUkId']['code'] ?? null,
            accountUkServiceSellerId: $data['accountUkServiceSellerId']['code'] ?? null,
            claim_number: $data['claimNumber']??null,
            is_not_read_sms: $data['isNotReadSMS']??null,
            is_not_read_document: $data['isNotReadDocument']??null
        );
    }

    private function makeClaimImage(array $data): ClaimImage
    {
        return new ClaimImage(
            id: $data['id'],
            name: $data['name'],
            type: DocumentType::from($data['documentType']['code']),
            url: action([ClaimController::class, 'getImage'], ['uri' => $data['url']]),
        );
    }

    private function timezoneCorrection($dateTime): ?Carbon
    {
        if (!$dateTime) {
            return null;
        }

        return Carbon::parse($dateTime)
            ->setTimezone('6')
            ->shiftTimezone(3);
    }

    private function timezoneArrivalCorrection($dateTime): ?Carbon
    {
        if (!$dateTime) {
            return null;
        }

        return Carbon::parse($dateTime);
    }

    private function makeClaimImageContent(array $data): ClaimImageContent
    {
        return new ClaimImageContent(
            $data['name'],
            $data['fileName'],
            $data['documentBody'],
            $data['mimeType'],
        );
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws InvalidArgumentException
     */
    private function getCatalogueItemTopParent(?string $parentId): ?ClaimCatalogueItem
    {
        $catalogueItemParent = null;
        if ($parentId) {
            $catalogueItemParent = $this->catalogueRepository->getOneById($parentId);
        }

        return $catalogueItemParent;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function getCached(string $key): mixed
    {
        return $this->cache->get($this->getCacheKey($key), []);
    }

    private function deleteCached(string $key): mixed
    {
        return $this->cache->get($this->deleteCached($key), []);
    }

    /**
     * @throws InvalidArgumentException
     */
    private function setCached(string $key, mixed $data): void
    {
        $this->cache->set($this->getCacheKey($key), $data);
    }

    private function getCacheKey(string $key): string
    {
        return "claims.$key";
    }

    /**
     * @return Claim[]
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    private function getActualClaimsByAccountNumber(string $accountNumber): array
    {
        $cachedClaims = collect($this->getCached($accountNumber));

        $cachedClaims = $cachedClaims->filter(function ($item) {
            return !is_array($item);
        });

        if (Cache::has("claims.$accountNumber" . "_lock")) {
            return $cachedClaims->toArray();
        }

        $shortClaims = $this->dynamicsCrmClient->getClaimsModifiedDateByAccountNumber($accountNumber);

        $actualClaimIds = Arr::pluck($shortClaims['claimUkShortList'], 'id');
        $newCachedClaims = $cachedClaims->filter(fn(Claim $claim) => in_array($claim->getId(), $actualClaimIds));

        if ($cachedClaims != $newCachedClaims) {
            $this->setCached($accountNumber, $newCachedClaims);
            $cachedClaims = collect($this->getCached($accountNumber));
        }

        usort($shortClaims['claimUkShortList'], static function ($claim1, $claim2) {
            return (Carbon::parse($claim1["modifiedOn"]) > Carbon::parse($claim2["modifiedOn"])) ? -1 : 1;
        });

        usort($shortClaims['claimUkShortList'], static function ($claim1, $claim2) {
            return ($claim1["isNotReadSMS"] && $claim2["isNotReadSMS"] === false) ? -1 : null;
        });

        $actualShortClaimIds = [];

        foreach ($shortClaims['claimUkShortList'] as $shortClaim) {
            $isCached = false;
            $isOutdated = false;
            $isNewMessage = false;

            foreach ($cachedClaims as $value) {
                if ($value->getId() == $shortClaim['id']) {
                    $isCached = true;

                    if ($isCached) {
                        $isOutdated = isset($shortClaim['modifiedOn'])
                            && Carbon::parse($shortClaim['modifiedOn']) > $value->getModifiedOn()
                                ?->avoidMutation()
                                ?->shiftTimezone(6)
                                ?->setTimezone(3);

                        if ($isOutdated) {
                            $isOutdated = true;
                        }

                        $rp = new ReflectionProperty(Claim::class, 'is_not_read_sms');

                        if ($rp->isInitialized($value)) {
                            $isNewMessage = $value->getIsNotReadSMS() != $shortClaim['isNotReadSMS'];
                        } else {
                            $isNewMessage = false;
                        }
                    }
                }
            }

            if (!$isCached || $isOutdated || $isNewMessage) {
                $actualShortClaimIds[] = $shortClaim['id'];
            }
        }

        if (count($actualShortClaimIds) > 10) {
            $actualsShort = array_chunk($actualShortClaimIds, 10);

            foreach ($actualsShort as $shortClaims) {
                //$this->makeClaim->makeClaims($shortClaims, $accountNumber);
                MakeClaims::dispatch($shortClaims, $accountNumber)->onQueue('default');
                sleep(0.5);
            }
        } elseif ($actualShortClaimIds) {
            $actualClaims = $this->dynamicsCrmClient->getClaimByIds($actualShortClaimIds)['claimUkList'];
            $actualClaimIds = Arr::pluck($actualClaims, 'id');

            foreach ($actualClaims as $k => $rawClaim) {
                try {
                    $actualClaims[$k] = $this->makeClaim($rawClaim, withDetails: false);
                } catch (Throwable) {
                    unset($actualClaims[$k]);
                }
            }

            $cachedClaims = $cachedClaims->filter(fn(Claim $claim) => !in_array($claim->getId(), $actualClaimIds));
            $cachedClaims->push(...$actualClaims);
            $this->setCached($accountNumber, $cachedClaims);
        }

        $cachedClaims = collect($this->getCached($accountNumber));

        return $cachedClaims->toArray();
    }

    private function getActualLastClaimByAccountNumber(string $accountNumber): array
    {
        $shortClaims = $this->dynamicsCrmClient->getClaimsModifiedDateByAccountNumber($accountNumber);

        $lastClaimWithNewMessage = null;
        $lastClaimsWithOutdated = null;

        foreach ($shortClaims['claimUkShortList'] as $shortClaim) {
            if (isset($lastClaimWithNewMessage['modifiedOn'])) {
                $isOutdated = Carbon::parse($shortClaim['modifiedOn']) > $lastClaimWithNewMessage['modifiedOn'];
            } else {
                $isOutdated = true;
            }

            $isNewMessage = $shortClaim['isNotReadSMS']??false;

            if ($isNewMessage) {
                if (!isset($lastClaimWithNewMessage['modifiedOn']) ||
                    Carbon::parse($shortClaim['modifiedOn']) > $lastClaimWithNewMessage['modifiedOn']
                ) {
                    $lastClaimWithNewMessage = $shortClaim;
                }
            }

            if ($isOutdated) {
                $lastClaimsWithOutdated = $shortClaim;
            }
        }

        if ($lastClaimWithNewMessage) {
            $actualClaims = $this->dynamicsCrmClient->getClaimByIds([$lastClaimWithNewMessage['id']])['claimUkList'];

            foreach ($actualClaims as $k => &$rawClaim) {
                try {
                    $rawClaim = $this->makeClaim($rawClaim, withDetails: false);
                } catch (Throwable) {
                    unset($actualClaims[$k]);
                }
            }

            return $actualClaims;
        } elseif ($lastClaimsWithOutdated) {
            $actualClaims = $this->dynamicsCrmClient->getClaimByIds([$lastClaimsWithOutdated['id']])['claimUkList'];

            foreach ($actualClaims as $k => &$rawClaim) {
                try {
                    $rawClaim = $this->makeClaim($rawClaim, withDetails: false);
                } catch (Throwable) {
                    unset($actualClaims[$k]);
                }

                break;
            }

            return $actualClaims;
        }

        return [];
    }

    /**
     * @throws InvalidArgumentException
     */
    public function updateClaimInCache(string $id): void
    {
        $rawClaim = $this->dynamicsCrmClient->getClaimById($id);

        if (!$rawClaim) {
            return;
        }

        $hasBeenFound = false;
        $accountNumber = $rawClaim['personalAccount'];
        $cachedClaims = collect($this->getCached($accountNumber));

        $cachedClaims = $cachedClaims->filter(function ($item) {
            return !is_array($item);
        });

        $cachedClaims = $cachedClaims->map(function (Claim $claim) use ($rawClaim, &$hasBeenFound) {
            $isTarget = $claim->getId() === $rawClaim['id'];

            if ($isTarget) {
                $hasBeenFound = true;
                $claim = $this->makeClaim($rawClaim, false);
            }

            return $claim;
        });

        if (!$hasBeenFound) {
            $cachedClaims->add($this->makeClaim($rawClaim, false));
        }

        $this->setCached($accountNumber, $cachedClaims);
    }

    public function getFiles(string $claimId)
    {
        return $this->dynamicsCrmClient->getFilesByClaimId($claimId);
    }

    public function getBody(string $url)
    {
        $returned_url = null;

        try {
            $returned_url =  $this->dynamicsCrmClient->getBodyByUrl($url)['documentBody'];
        } catch (Throwable $e) {
            //
        }

        return $returned_url;
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function getLastClaims(array $accountNumbers)
    {
        try {
            $actualClaims = $this->dynamicsCrmClient->getClaimsByPersonalAccounts($accountNumbers);
        } catch (Throwable $exception) {
            $actualClaims = null;
        }
        if ($actualClaims != null) {
            $claims = $actualClaims['claimUkList'];
//
//            foreach ($claims as $k => &$rawClaim) {
//                try {
//                    $rawClaim = $this->makeClaim($rawClaim, withDetails: false);
//                } catch (Throwable) {
//                    unset($claims[$k]);
//                }
//            }

            $claim_element = null;

            $lastClaim = collect($actualClaims['claimUkList'])->sortByDesc('createdOn')->first();

//            foreach ($claims as $claim_el) {
//                // phpcs:disable
//                if ($claim_element==null??(Carbon::parse($claim_element['createdOn'])<Carbon::parse($claim_el['createdOn'])) &&
//                    // phpcs:enable
//                    ($claim_el['isNotReadSMS']||$claim_el['isNotReadDocument'])
//                ) {
//                    $claim_element = $claim_el;
//                }
//            }

            return $this->makeClaim($lastClaim, withDetails: false);
        }

        return null;
    }
}
