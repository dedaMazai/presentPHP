<?php

namespace App\Services\Claim;

use App\Http\Api\External\V1\Controllers\ClaimController;
use App\Models\Claim\Claim;
use App\Models\Claim\ClaimCatalogue\ClaimCatalogueItem;
use App\Models\Claim\ClaimExecutor;
use App\Models\Claim\ClaimImage;
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
use App\Models\User\User;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Throwable;

/**
 * Class ClaimService
 *
 * @package App\Services\Claim
 */
class MakeClaim
{
    public function __construct(
        private ClaimCatalogueRepository $catalogueRepository,
        private DynamicsCrmClient $dynamicsCrmClient,
        private CacheInterface $cache
    ) {
    }

    public function makeClaims($shortClaims, $accountNumber)
    {
        $actualClaims = $this->dynamicsCrmClient->getClaimByIds($shortClaims)['claimUkList'];
        $actualClaimIds = Arr::pluck($actualClaims, 'id');
        $hash = mt_rand();

        foreach ($actualClaims as $k => &$rawClaim) {
            try {
                $rawClaim = $this->makeClaim($rawClaim, withDetails: false);
            } catch (Throwable) {
                unset($actualClaims[$k]);
            }
        }

        while (true) {
            while (Cache::get("claims.$accountNumber" . "_lock") != $hash) {
                if (Cache::has("claims.$accountNumber" . "_lock")) {
                    continue;
                }

                $this->setCached($accountNumber . "_lock", $hash);
            }
            if (Cache::get("claims.$accountNumber" . "_lock") == $hash) {
                break;
            }
        }

        $cachedClaims = collect(Cache::get("claims.$accountNumber"));
        $cachedClaims = $cachedClaims->filter(fn(Claim $claim) => !in_array($claim->getId(), $actualClaimIds));
        $cachedClaims->push(...$actualClaims);
        $this->setCached($accountNumber, $cachedClaims);

        Cache::forget("claims.$accountNumber"."_lock");
    }

    /**
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BadRequestException
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
                $catalogueItem = $this
                    ->catalogueRepository
                    ->getOneById($service['serviceCatalogUkId'], $service['amountKop']);
                $catalogueItemParent = $this->getCatalogueItemTopParent($catalogueItem->getParentId());

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
        $code = $data['invoiceStatus']['code'] ?? '';
        if ($code === '7') {
            $code = '1';
        }

        return new Claim(
            id: $data['id'],
            number: $data['claimNumber'] ?? null,
            theme: ClaimTheme::from($data['incidentClassificationCode']['code']) ?? null,
            status: ClaimStatus::from($data['status']['code']),
            createdAt: $this->timezoneCorrection($data['createdOn'] ?? null),
            closedAt: $this->timezoneCorrection($data['closedOn'] ?? null),
            paymentStatus: ClaimPaymentStatus::tryFrom($code),
            comment: $data['description'] ?? null,
            arrivalDate: $this->timezoneArrivalCorrection($data['arrivalDate'] ?? null),
            paymentDate: $this->timezoneCorrection($data['paymentDate'] ?? null),
            totalPayment: $data['totalPaymentKop'] ?? null,
            scheduledStart: $this->timezoneCorrection($data['scheduledStart'] ?? null),
            scheduledEnd: $this->timezoneCorrection($data['scheduledEnd'] ?? null),
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
            is_not_read_document: $data['isNotReadDocument']??null,
        );
    }

    private function getCatalogueItemTopParent(?string $parentId): ?ClaimCatalogueItem
    {
        $catalogueItemParent = null;
        if ($parentId) {
            $catalogueItemParent = $this->catalogueRepository->getOneById($parentId);
        }

        return $catalogueItemParent;
    }

    public function getClaimImagesByClaimId(string $claimId): array
    {
        $data = $this->dynamicsCrmClient->getDocumentsByClaimId($claimId);

        return array_map(fn($data) => $this->makeClaimImage($data), $data['documentList'] ?? []);
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

    private function setCached(string $key, mixed $data): void
    {
        $this->cache->set($this->getCacheKey($key), $data);
    }

    private function getCacheKey(string $key): string
    {
        return "claims.$key";
    }

    private function getCached(string $key): mixed
    {
        return $this->cache->get($this->getCacheKey($key), []);
    }
}
