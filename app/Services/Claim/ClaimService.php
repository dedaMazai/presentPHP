<?php

namespace App\Services\Claim;

use App\Jobs\UploadFilesJob;
use App\Models\Claim\Claim;
use App\Models\Claim\ClaimCatalogue\ClaimCatalogueItemGroup;
use App\Models\Claim\ClaimCatalogue\ClaimCatalogueItemSellerType;
use App\Models\Claim\LastClaim;
use App\Models\User\User;
use App\Services\Claim\Dto\ClaimImageDto;
use App\Services\Claim\Dto\SaveClaimAppealDto;
use App\Services\Claim\Dto\SaveClaimAttachmentDto;
use App\Services\Claim\Dto\SaveClaimMarketplaceDto;
use App\Services\Claim\Dto\SaveClaimMarketplaceItemDto;
use App\Services\Claim\Dto\SaveClaimPassDto;
use App\Services\Claim\Dto\SaveClaimRequestDto;
use App\Services\Claim\Dto\SaveClaimSosDto;
use App\Services\Claim\Dto\SaveClaimVisitDto;
use App\Services\Claim\Dto\SaveClaimWarrantyDto;
use App\Services\Claim\Dto\SetClaimPaidDto;
use App\Services\Crm\CrmClient;
use App\Services\Document\DocumentRepository;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\DynamicsCrm\Exceptions\UnableToCancelClaimException;
use App\Services\Receipt\ReceiptRepository;
use Assert\Assert;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class ClaimService
 *
 * @package App\Services\Claim
 */
class ClaimService
{
    public function __construct(
        private DynamicsCrmClient $dynamicsCrmClient,
        private ClaimRepository $repository,
        private CrmClient $crmClient,
        private ReceiptRepository $receiptRepository,
        private DocumentRepository $documentRepository,
        private ClaimCatalogueRepository $claimCatalogueRepository,
    ) {
    }

    /**
     * @param  string  $id
     * @param  int  $code
     *
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws InvalidArgumentException
     */
    public function accept(string $id, int $code): void
    {
        $this->dynamicsCrmClient->acceptClaimById($id, $code);
        $this->repository->updateClaimInCache($id);
    }

    /**
     * @param  string  $id
     * @param  ClaimImageDto[]  $imageDtos
     *
     * @throws NotFoundException
     * @throws BadRequestException
     * @throws InvalidArgumentException
     */
    public function reopen(string $id, array $imageDtos): void
    {
        $this->dynamicsCrmClient->reopenClaimById($id, $imageDtos);
        $this->repository->updateClaimInCache($id);
    }

    /**
     * @param  string  $id
     * @param  int  $rating
     *
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws InvalidArgumentException
     */
    public function rate(string $id, int $rating): void
    {
        $this->dynamicsCrmClient->rateClaimById($id, $rating);
        $this->repository->updateClaimInCache($id);
    }

    /**
     * @param  string  $id
     * @param  string  $comment
     *
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function comment(string $id, string $comment): void
    {
        $this->dynamicsCrmClient->commentClaimById($id, $comment);
    }

    /**
     * @throws NotFoundException
     * @throws UnableToCancelClaimException
     * @throws BadRequestException
     */
    public function cancel(string $id): void
    {
        logger()->debug('ClaimService::cancel', [
            'claim_id' => $id,
        ]);
        $this->dynamicsCrmClient->cancelClaim($id);
        $this->repository->updateClaimInCache($id);
    }

    /**
     * @throws NotFoundException
     * @throws UnableToCancelClaimException
     * @throws BadRequestException
     */
    public function decline(string $id): void
    {
        logger()->debug('ClaimService::decline', [
            'claim_id' => $id,
        ]);
        $this->dynamicsCrmClient->declineClaim($id);
        $this->repository->updateClaimInCache($id);
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     * @throws InvalidArgumentException
     */
    public function savePass(string $accountNumber, User $user, SaveClaimPassDto $dto): void
    {
        logger()->debug('ClaimService::savePass', [
            'account_number' => $accountNumber,
            'user_id' => $user->id,
        ]);
        $claimRawData = $this->dynamicsCrmClient->saveClaimPass($accountNumber, $user, $dto);
        $this->updateLastClaimId($accountNumber, $claimRawData);
        $this->repository->updateClaimInCache($claimRawData['id']);
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     * @throws InvalidArgumentException
     */
    public function saveSos(string $accountNumber, User $user, SaveClaimSosDto $dto): void
    {
        logger()->debug('ClaimService::saveSos', [
            'account_number' => $accountNumber,
            'user_id' => $user->id,
        ]);
        $claimRawData = $this->dynamicsCrmClient->saveClaimSos($accountNumber, $user, $dto);
        $this->updateLastClaimId($accountNumber, $claimRawData);
        $this->repository->updateClaimInCache($claimRawData['id']);
    }

    /**
     * @param  string  $accountNumber
     * @param  User  $user
     * @param  SaveClaimRequestDto[]  $saveClaimRequestDtos
     *
     * @return void
     * @throws BadRequestException|NotFoundException|InvalidArgumentException
     */
    public function saveRequest(string $accountNumber, User $user, array $saveClaimRequestDtos): void
    {
        Assert::thatAll($saveClaimRequestDtos)->isInstanceOf(SaveClaimRequestDto::class);

        logger()->debug('ClaimService::saveRequest', [
            'account_number' => $accountNumber,
            'user_id' => $user->id,
        ]);

        foreach ($saveClaimRequestDtos as $saveClaimRequestDto) {
            try {
                $claimRawData = $this->dynamicsCrmClient->saveClaimRequest($accountNumber, $user, $saveClaimRequestDto);
            } catch (BadRequestException|NotFoundException $e) {
                logger()->error('Dynamics CRM client', [
                    'message' => $e->getMessage(),
                    'account_number' => $accountNumber,
                    'user_id' => $user->id,
                    'trace' => $e->getTrace(),
                ]);

                throw $e;
            }
        }

        if (isset($claimRawData)) {
            $this->updateLastClaimId($accountNumber, $claimRawData ?? null);
            $this->repository->updateClaimInCache($claimRawData['id']);
        }
    }

    /**
     * @param  string  $accountNumber
     * @param  User  $user
     * @param  SaveClaimRequestDto[]  $saveClaimRequestDtos
     *
     * @throws BadRequestException|NotFoundException|InvalidArgumentException
     */
    public function saveCreate(string $accountNumber, User $user, array $saveClaimRequestDtos)
    {
        Assert::thatAll($saveClaimRequestDtos)->isInstanceOf(SaveClaimRequestDto::class);

        logger()->debug('ClaimService::saveRequest', [
            'account_number' => $accountNumber,
            'user_id' => $user->id,
        ]);

        foreach ($saveClaimRequestDtos as $saveClaimRequestDto) {
            try {
                $claimRawData = $this->dynamicsCrmClient->saveCreateRequest(
                    $accountNumber,
                    $user,
                    $saveClaimRequestDto
                );
            } catch (BadRequestException|NotFoundException $e) {
                logger()->error('Dynamics CRM client', [
                    'message' => $e->getMessage(),
                    'account_number' => $accountNumber,
                    'user_id' => $user->id,
                    'trace' => $e->getTrace(),
                ]);

                throw $e;
            }
        }

        if (isset($claimRawData)) {
            $this->updateLastClaimId($accountNumber, $claimRawData ?? null);
            $this->repository->updateClaimInCache($claimRawData['id']);
        }

        return $claimRawData['id'];
    }

    public function uploadImages($images, $user, $claimId)
    {
        foreach ($images as $image) {
            $filename = $image->getClientOriginalName();
            $filepath = Storage::disk('local')->put($filename, $image);

            $img = [
                'path' => $filepath,
                'originalName' => $image->getClientOriginalName(),
                'extension' => $image->getClientOriginalExtension(),
                'mimeType' => $image->getClientMimeType(),
                'size' => $image->getSize()
            ];

            UploadFilesJob::dispatch($img, $user, $claimId)->onQueue('default');
        }
    }

    /**
     * @param  string  $accountNumber
     * @param  User  $user
     * @param  SaveClaimAppealDto[]  $saveClaimAppealDtos
     *
     * @return void
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws InvalidArgumentException
     */
    public function saveAppeal(string $accountNumber, User $user, array $saveClaimAppealDtos): void
    {
        Assert::thatAll($saveClaimAppealDtos)->isInstanceOf(SaveClaimAppealDto::class);

        logger()->debug('ClaimService::saveAppeal', [
            'account_number' => $accountNumber,
            'user_id' => $user->id,
        ]);

        foreach ($saveClaimAppealDtos as $saveClaimAppealDto) {
            $claimRawData = $this->dynamicsCrmClient->saveClaimAppeal($accountNumber, $user, $saveClaimAppealDto);
        }

        if (isset($claimRawData)) {
            $this->updateLastClaimId($accountNumber, $claimRawData ?? null);
            $this->repository->updateClaimInCache($claimRawData['id']);
        }
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     * @throws InvalidArgumentException
     */
    public function saveVisit(string $accountNumber, User $user, SaveClaimVisitDto $dto): void
    {
        logger()->debug('ClaimService::saveVisit', [
            'account_number' => $accountNumber,
            'user_id' => $user->id,
        ]);
        $claimRawData = $this->dynamicsCrmClient->saveClaimVisit($accountNumber, $user, $dto);
        $this->updateLastClaimId($accountNumber, $claimRawData);
        $this->repository->updateClaimInCache($claimRawData['id']);
    }

    /**
     * @param  string  $accountNumber
     * @param  User  $user
     * @param  SaveClaimWarrantyDto[]  $saveClaimWarrantyDtos
     *
     * @return void
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws InvalidArgumentException
     */
    public function saveWarranty(string $accountNumber, User $user, array $saveClaimWarrantyDtos): void
    {
        Assert::thatAll($saveClaimWarrantyDtos)->isInstanceOf(SaveClaimWarrantyDto::class);

        logger()->debug('ClaimService::saveWarranty', [
            'account_number' => $accountNumber,
            'user_id' => $user->id,
        ]);

        foreach ($saveClaimWarrantyDtos as $saveClaimWarrantyDto) {
            $claimRawData = $this->dynamicsCrmClient->saveClaimWarranty($accountNumber, $user, $saveClaimWarrantyDto);
        }

        if (isset($claimRawData)) {
            $this->updateLastClaimId($accountNumber, $claimRawData);
            $this->repository->updateClaimInCache($claimRawData['id']);
        }
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException|InvalidArgumentException
     */
    public function saveMarketplace(string $accountNumber, User $user, SaveClaimMarketplaceDto $dto): void
    {
        logger()->debug('ClaimService::saveMarketplace', [
            'account_number' => $accountNumber,
            'user_id' => $user->id,
            'count' => count($dto->imageDtos),
        ]);
        $claimRawData = $this->dynamicsCrmClient->saveClaimMarketplace($accountNumber, $user, $dto);
        $this->updateLastClaimId($accountNumber, $claimRawData);
        $this->repository->updateClaimInCache($claimRawData['id']);
    }

    public function saveAttachmentFiles(string $claim_id, User $user, SaveClaimAttachmentDto $dto): void
    {
        logger()->debug('ClaimService::saveAttachmentFiles', [
            'claim_id' => $claim_id,
            'user_id' => $user->id,
        ]);
        $this->dynamicsCrmClient->saveClaimAttachment($dto);
    }



    /**
     * @throws NotFoundException
     * @throws BadRequestException
     * @throws InvalidArgumentException
     */
    public function groupClaimsForMarketplace(array $claimCatalogItems): array
    {
        $groupedClaims = [];
        $groups = [];

        foreach ($claimCatalogItems as $catalogueItem) {
            $claim = $this->claimCatalogueRepository->getOneById($catalogueItem['id']);
            $targetClaim = $claim;

            if (!$claim) {
                continue;
            }

            while ($claim->getParentId()
                && (
                    $claim->getGroup()->equals(ClaimCatalogueItemGroup::lvl4())
                    || $claim->getGroup()->equals(ClaimCatalogueItemGroup::lvl5())
                    || $claim->getGroup()->equals(ClaimCatalogueItemGroup::service())
                )
            ) {
                $claim = $this->claimCatalogueRepository->getOneById($claim->getParentId());

                if (!$claim) {
                    continue 2;
                }
            }

            // если не задан sellerTypeCode то выносим в отдельную группу, чтобы заявка создалась отдельно от других
            $sellerTypeCode = $targetClaim->getSellerTypeCode()['code'] ?? uniqid();

            if ($catalogueItem['count'] == null || $targetClaim == null) {
                continue;
            };

            $groupedClaims[$sellerTypeCode][$claim->getId()][] =
                new SaveClaimMarketplaceItemDto(
                    $targetClaim,
                    $catalogueItem['count'],
                );

            $groups[$targetClaim->getSellerTypeCode()['name']??'no name'][$claim->getGroup()->label . '|' .
            $claim->getName()][$targetClaim->getName()] = $targetClaim->getId();
        }

        logger()->debug('groupClaimsForMarketplace: grouped claims', [
            'groups' => $groups,
        ]);

        $claimsArray = [];

        foreach ($groupedClaims as $groupBySeller) {
            foreach ($groupBySeller as $groupByCategory) {
                $claimsArray[] = $groupByCategory;
            }
        }

        return $claimsArray;
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function setPaid(User $user, SetClaimPaidDto $dto, string $paymentMethod): void
    {
        $this->crmClient->setClaimPaid($user, $dto, $paymentMethod);
        $this->dynamicsCrmClient->setClaimPaid($user, $dto, $paymentMethod);
    }

    public function updatePaidStatus($id): void
    {
        $this->dynamicsCrmClient->paidClaimById($id);
    }

    public function getLastClaim(User $user): ?LastClaim
    {
        $accountNumbers = $user->relationships()->pluck('account_number')->toArray();

        /** @var LastClaim $lastClaim */
        $lastClaim = LastClaim::whereIn('account_number', $accountNumbers)->orderByDesc('claim_created_at')->first();

        return $lastClaim;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function updateLastClaimId(string $accountNumber, ?array $claimRawData): void
    {
        if (isset($claimRawData['id'])) {
            LastClaim::updateOrCreate([
                'account_number' => $accountNumber,
            ], [
                'account_number' => $accountNumber,
                'claim_id' => $claimRawData['id'],
                'claim_created_at' => new Carbon($claimRawData['createdOn']),
            ]);
        }
    }

    public function getClaimReceipt(Claim $claim): ?array
    {
        $receipts = $this->dynamicsCrmClient->getClaimReceipts($claim->getId());

        return collect($receipts['documentList'] ?? [])
            ->sortByDesc('createdOn')
            ->first();
    }

    public function findClaim(string $id): Claim
    {
        $claim = $this->repository->getOneById($id);

        if ($claim === null) {
            throw new NotFoundException('Claim not found.');
        }

        return $claim;
    }

    public function getClaimFiles(string $id)
    {
        $files = $this->repository->getFiles($id);

        if ($files === null) {
            return null;
        }

        foreach ($files['documentList'] as $key => $value) {
            if (!isset($files['documentList'][$key]['urlPreview'])) {
                continue;
            }

            $files['documentList'][$key]['urlPreview'] = $this->repository
                ->getBody($files['documentList'][$key]['urlPreview']);
        }

        return collect($files ?? [])
//            ->where('isCustomerAvailable', '=', "true")
            ->sortByDesc('createdOn');
    }

    public function getClaimFileById(string $claim_id, string $document_id)
    {
        $files = $this->repository->getFiles($claim_id);

        if ($files === null) {
            throw new NotFoundException('Files not found.');
        }

        foreach ($files['documentList'] as $value) {
            if ($value['id'] == $document_id) {
                $document = $this->documentRepository->getDocumentById($document_id);
                break;
            }
        }

        return $document;
    }

    public function getSellerForClaim(Claim $claim): ?string
    {
        /** @var \App\Models\Claim\ClaimService $claimService */
        $claimService = collect($claim->getServices())
            ->first(fn($cService) => $cService->getCatalogueItem()->getSellerTypeCode());

        if (!$claimService) {
            print $claim->getNumber();
            logger()->error(
                'ClaimService->getSellerForClaim: claim doesn\'t have a service with seller type code',
                [
                    'claim_id' => $claim->getId()
                ]
            );

            return null;
        }

        return match ($claimService->getCatalogueItem()->getSellerTypeCode()['code']) {
            ClaimCatalogueItemSellerType::uk()->value => $claim->getAccountUkId(),
            ClaimCatalogueItemSellerType::additionalUkServices()->value => $claim->getAccountUkServiceSellerId(),
            ClaimCatalogueItemSellerType::external()->value => null,
        };
    }
}
