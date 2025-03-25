<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Auth\VerificationCode\VerificationCase;
use App\Auth\VerificationCode\VerificationCodeManager;
use App\Http\Api\External\V1\Requests\Claim\SaveClaimAppealRequest;
use App\Http\Api\External\V1\Requests\Claim\SaveClaimAttachmentRequest;
use App\Http\Api\External\V1\Requests\Claim\SaveClaimMarketplaceRequest;
use App\Http\Api\External\V1\Requests\Claim\SaveClaimPassRequest;
use App\Http\Api\External\V1\Requests\Claim\SaveClaimRequestRequest;
use App\Http\Api\External\V1\Requests\Claim\SaveClaimSosRequest;
use App\Http\Api\External\V1\Requests\Claim\SaveClaimVisitRequest;
use App\Http\Api\External\V1\Requests\Claim\SaveClaimWarrantyRequest;
use App\Http\Api\External\V1\Traits\VerifyCode;
use App\Http\Resources\AccountCollection;
use App\Http\Resources\Claim\ClaimAttachmentCollection;
use App\Http\Resources\Claim\ClaimAttachmentResource;
use App\Http\Resources\Claim\ClaimCatalogueItemCollection;
use App\Http\Resources\Claim\ClaimCatalogueItemResource;
use App\Http\Resources\Claim\ClaimCatalogueSearchResultResource;
use App\Http\Resources\Claim\ClaimCatalogueThemeResource;
use App\Http\Resources\Claim\ClaimListCollection;
use App\Http\Resources\Claim\ClaimListResource;
use App\Http\Resources\Claim\DetailClaimCollection;
use App\Http\Resources\Claim\DetailClaimResource;
use App\Http\Resources\Claim\LastClaimResource;
use App\Models\Claim\Claim;
use App\Models\Claim\ClaimCatalogue\ClaimCatalogueItem;
use App\Models\Claim\ClaimFilter\ClaimFilterStatus;
use App\Models\Claim\ClaimPass\ClaimPassCarType;
use App\Models\Claim\ClaimPass\ClaimPassType;
use App\Models\Claim\ClaimTheme;
use App\Models\Document\DocumentType;
use App\Services\Account\AccountRepository;
use App\Services\Account\AccountService;
use App\Services\Claim\ClaimCatalogueRepository;
use App\Services\Claim\ClaimRepository;
use App\Services\Claim\ClaimService;
use App\Services\Claim\Dto\ClaimImageDto;
use App\Services\Claim\Dto\ClaimPassCarItemDto;
use App\Services\Claim\Dto\ClaimPassHumanItemDto;
use App\Services\Claim\Dto\SaveClaimAppealDto;
use App\Services\Claim\Dto\SaveClaimAttachmentDto;
use App\Services\Claim\Dto\SaveClaimMarketplaceDto;
use App\Services\Claim\Dto\SaveClaimPassDto;
use App\Services\Claim\Dto\SaveClaimRequestDto;
use App\Services\Claim\Dto\SaveClaimSosDto;
use App\Services\Claim\Dto\SaveClaimVisitDto;
use App\Services\Claim\Dto\SaveClaimWarrantyDto;
use App\Services\Claim\LastClaimService;
use App\Services\Contract\ContractRepository;
use App\Services\Contract\ContractService;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\DynamicsCrm\Exceptions\UnableToCancelClaimException;
use App\Services\User\UserService;
use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Facades\Image;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Class ClaimController
 *
 * @package App\Http\Api\External\V1\Controllers
 */
class ClaimController extends Controller
{
    use VerifyCode;

    public function __construct(
        private VerificationCodeManager $verificationCodeManager,
        private ClaimRepository $repository,
        private ClaimCatalogueRepository $catalogueRepository,
        private ClaimService $claimService,
        private UserService $userService,
    ) {
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws ValidationException
     * @throws InvalidArgumentException
     */
    public function getCatalogue(Request $request): Response
    {
        $this->validate($request, [
            'theme_id' => [
                'required',
                Rule::in(ClaimTheme::toValues()),
            ],
        ]);
        $theme = ClaimTheme::from($request->input('theme_id'));

        $claimCatalogueTheme = $this->catalogueRepository->getAllByTheme($theme);

        return response()->json(new ClaimCatalogueThemeResource($claimCatalogueTheme));
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws ValidationException
     * @throws InvalidArgumentException
     */
    public function getAccountCatalogue(string $accountNumber, Request $request): Response
    {
        $this->validate($request, [
            'theme_id' => [
                'required',
                Rule::in(ClaimTheme::toValues()),
            ],
        ]);
        $theme = ClaimTheme::from($request->input('theme_id'));

        $claimCatalogueTheme = $this->catalogueRepository->getAllAccountByTheme($theme, $accountNumber);

        return response()->json(new ClaimCatalogueThemeResource($claimCatalogueTheme));
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws ValidationException
     * @throws InvalidArgumentException
     */
    public function getCatalogueSearch(Request $request): Response
    {
        $this->validate($request, [
            'theme_id' => [
                'required',
                Rule::in(ClaimTheme::toValues()),
            ],
            'query' => 'required|string',
        ]);
        $theme = ClaimTheme::from($request->input('theme_id'));

        $searchResults = $this->catalogueRepository->getSearchResults($theme, $request->input('query'));
        $catalogues = $this->findAllClaimCatalogueItem();

        foreach ($searchResults as $key => $category) {
            foreach ($category as $k => &$value) {
                $parent = $value->getParentId();
                while ($parent != '') {
                    foreach ($catalogues as $rawItem) {
                        if ($rawItem['id'] == $parent) {
                            $catalogue = $this->catalogueRepository->makeClaimCatalogueItem($rawItem);
                        }
                    }

                    if (!$catalogue->getIsDisplayedInLk()) {
                        unset($searchResults[$key][$k]);
                        $parent = '';
                    } else {
                        $parent = $catalogue->getParentId();
                    }
                }
            }
        }


        return response()->json(new ClaimCatalogueSearchResultResource($searchResults));
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws ValidationException
     * @throws InvalidArgumentException
     */
    public function getAccountCatalogueSearch(string $accountNumber, Request $request): Response
    {
        $this->validate($request, [
            'theme_id' => [
                'required',
                Rule::in(ClaimTheme::toValues()),
            ],
            'query' => 'required|string',
        ]);
        $theme = ClaimTheme::from($request->input('theme_id'));

        $searchResults = $this->catalogueRepository->getAccountSearchResults(
            $theme,
            $request->input('query'),
            $accountNumber
        );

        return response()->json(new ClaimCatalogueSearchResultResource($searchResults));
    }

    /**
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    public function getCatalogueItem(string $id): Response
    {
        $claimCatalogueItem = $this->findClaimCatalogueItem($id);

        return response()->json(new ClaimCatalogueItemResource($claimCatalogueItem));
    }

    /**
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    public function getAccountCatalogueItem(string $accountNumber, string $id): Response
    {
        $claimCatalogueItem = $this->findAccountCatalogueItem($accountNumber, $id);

        return response()->json(new ClaimCatalogueItemResource($claimCatalogueItem));
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws ValidationException
     * @throws InvalidArgumentException
     */
    public function getPopularServices(Request $request): Response
    {
        $this->validate($request, [
            'theme_id' => [
                'required',
                Rule::in(ClaimTheme::toValues()),
            ],
        ]);
        $theme = ClaimTheme::from($request->input('theme_id'));

        $claimCataloguePopularServices = $this->catalogueRepository->getPopularServicesByTheme($theme);

        return response()->json(new ClaimCatalogueItemCollection($claimCataloguePopularServices));
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws ValidationException
     * @throws InvalidArgumentException
     */
    public function getAccountPopularServices(string $accountNumber, Request $request): Response
    {
        $this->validate($request, [
            'theme_id' => [
                'required',
                Rule::in(ClaimTheme::toValues()),
            ],
        ]);
        $theme = ClaimTheme::from($request->input('theme_id'));

        $claimCataloguePopularServices = $this->catalogueRepository->getAccountPopularServicesByTheme(
            $accountNumber,
            $theme
        );

        return response()->json(new ClaimCatalogueItemCollection($claimCataloguePopularServices));
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws ValidationException
     */
    public function index(string $accountNumber, Request $request): Response
    {
        $this->validate($request, [
            'theme_id' => [
                Rule::in(ClaimTheme::toValues()),
            ],
            'date_from' => 'date',
            'date_to' => 'date',
            'claim_number' => 'string',
            'is_not_read_sms' => 'bool',
            'is_not_read_document' => 'bool',
            'statuses' => 'array',
            'statuses.*' => [
                Rule::in(ClaimFilterStatus::toValues()),
            ],
        ]);
        $theme = ClaimTheme::tryFrom($request->input('theme_id', ''));
        $dateFrom = $request->input('date_from') ? new Carbon($request->input('date_from')) : null;
        $dateTo = $request->input('date_to') ? new Carbon($request->input('date_to')) : null;
        $isNotReadSms = $request->input('is_not_read_sms');
        $isNotReadDocument = $request->input('is_not_read_document');
        $claimNumber = $request->input('claim_number');
        $filterStatuses = [];
        if (is_array($request->input('statuses'))) {
            foreach ($request->input('statuses') as $status) {
                $filterStatuses[] = ClaimFilterStatus::from($status);
            }
        }

        $claims = $this->repository->getAll(
            $accountNumber,
            $this->getAuthUser(),
            $theme,
            $dateFrom,
            $dateTo,
            $filterStatuses,
            $claimNumber,
            $isNotReadSms,
            $isNotReadDocument
        );

        return response()->json(new ClaimListCollection($claims));
    }

    public function show(string $id): Response
    {
        $claim = $this->claimService->findClaim($id);
        $receipt = $this->claimService->getClaimReceipt($claim);

        return response()->json(new DetailClaimResource(["claim" => $claim, "receipt" =>$receipt]));
    }

    /**
     * @throws NotFoundException
     * @throws ValidationException
     * @throws BadRequestException
     */
    public function getImage(Request $request): Response
    {
        $this->validate($request, [
            'uri' => 'required|string',
        ]);

        $imageFile = $this->repository->getClaimImageContentByUri($request->input('uri'));

        return response(base64_decode($imageFile->getDocumentBody()))
            ->header('Content-Type', $imageFile->getMimeType());
    }

    /**
     * @throws AuthenticationException
     */
    public function getLastClaim(LastClaimService $lastClaimService): Response
    {
        $claim = $lastClaimService->getLastClaimByAccounts($this->getAuthUser());

        if (!$claim) {
            return $this->response();
        }

        $claim['receipt'] = $this->claimService->getClaimReceipt($claim['claim']);

        return response()->json(new LastClaimResource($claim));
    }

    /**
     * @param  string  $id
     * @param  Request  $request
     *
     * @return Response
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws ValidationException
     * @throws AuthenticationException
     */
    public function accept(string $id, Request $request): Response
    {
        $this->validate($request, [
            'verification_code' => 'required|string',
        ]);

        $this->verifyCode(
            VerificationCase::claimAcceptance(),
            $this->getAuthUser()->phone,
            $request->input('verification_code'),
        );
        $this->verificationCodeManager->forget(VerificationCase::claimAcceptance(), $this->getAuthUser()->phone);

        $this->claimService->accept($id, $request->input('verification_code'));

        return $this->empty();
    }

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     * @throws ValidationException
     */
    public function reopen(string $id, Request $request): Response
    {
        $this->validate($request, [
            'images' => 'array',
            'images.*' => 'file',
        ]);

        $this->claimService->reopen($id, $this->getImageDtos($request->file('images', [])));

        return $this->empty();
    }

    /**
     * @param  string  $id
     * @param  Request  $request
     *
     * @return Response
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function rate(string $id, Request $request): Response
    {
        $this->validate($request, [
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $this->claimService->rate($id, $request->input('rating'));

        return $this->empty();
    }

    /**
     * @param  string  $id
     * @param  Request  $request
     *
     * @return Response
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function comment(string $id, Request $request): Response
    {
        $this->validate($request, [
            'comment_quality' => 'required|string',
        ]);

        $this->claimService->comment($id, $request->input('comment_quality'));

        return $this->empty();
    }

    /**
     * @throws NotFoundException
     * @throws UnableToCancelClaimException
     * @throws BadRequestException
     */
    public function cancel(string $id): Response
    {
        $claim = $this->findClaim($id);
        if ((!$claim->getPaymentStatus() || $claim->getPaymentStatus()->isCancelable())
            && !$claim->getStatus()->isCancelable()
            && !$claim->getTheme()->equals(ClaimTheme::pass())
        ) {
            throw new BadRequestHttpException('Unable to cancel: claim is not cancelable.');
        }

        $this->claimService->cancel($id);

        return $this->empty();
    }

    /**
     * @throws NotFoundException
     * @throws UnableToCancelClaimException
     * @throws BadRequestException
     */
    public function decline(string $id): Response
    {
        $this->claimService->decline($id);

        return $this->empty();
    }

    /**
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws BadRequestException
     * @throws InvalidArgumentException
     */
    public function storePass(string $accountNumber, SaveClaimPassRequest $request): Response
    {
        $humanItemDto = null;
        $carItemDto = null;
        $passType = ClaimPassType::from($request->input('pass_type'));
        if ($passType->equals(ClaimPassType::human())) {
            $humanItemDto = new ClaimPassHumanItemDto($request->input('full_name'));
        } elseif ($passType->equals(ClaimPassType::car())) {
            $carItemDto = new ClaimPassCarItemDto(
                carType: ClaimPassCarType::from($request->input('car_type')),
                number: $request->input('car_number'),
            );
        }

        $dto = new SaveClaimPassDto(
            passType: $passType,
            arrivalDate: Carbon::parse($request->input('arrival_date')),
            comment: $request->input('comment'),
            humanItemDto: $humanItemDto,
            carItemDto: $carItemDto,
            imageDtos: $this->getImageDtos($request->file('images', [])),
        );
        $this->claimService->savePass($accountNumber, $this->getAuthUser(), $dto);

        return $this->empty();
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws AuthenticationException
     * @throws InvalidArgumentException
     */
    public function storeSos(string $accountNumber, SaveClaimSosRequest $request): Response
    {
        $claimCatalogueItem = $this->findClaimCatalogueItem($request->input('claim_catalogue_item_id'));

        $dto = new SaveClaimSosDto(
            claimCatalogueItem: $claimCatalogueItem,
            comment: $request->input('comment'),
            imageDtos: $this->getImageDtos($request->file('images', [])),
        );
        $this->claimService->saveSos($accountNumber, $this->getAuthUser(), $dto);

        return $this->empty();
    }

    /**
     * @throws BadRequestException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws InvalidArgumentException
     */
    public function storeRequest(string $accountNumber, SaveClaimRequestRequest $request): Response
    {
        $claimCatalogueItems = $this->splitCatalogueItemsByParent($request->input('claim_catalogue_item_ids'));

        $saveClaimRequestDtos = [];
        foreach ($claimCatalogueItems as $claimCatalogueItem) {
            $saveClaimRequestDtos[] = new SaveClaimRequestDto(
                claimCatalogueItems: $claimCatalogueItem,
                comment: $request->input('comment'),
                arrivalDate: $request->input('arrival_date') ?
                    Carbon::parse($request->input('arrival_date')) : null,
                imageDtos: $this->getImageDtos($request->file('images', [])),
            );
        }

        try {
            $this->claimService->saveRequest($accountNumber, $this->getAuthUser(), $saveClaimRequestDtos);
        } catch (BadRequestException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e, $e->getCode());
        } catch (NotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e, $e->getCode());
        }

        return $this->empty();
    }

    /**
     * @throws BadRequestException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws InvalidArgumentException
     */
    public function storeCreate(string $accountNumber, SaveClaimRequestRequest $request): Response
    {
        $claimCatalogueItems = $this->splitCatalogueItemsByParent($request->input('claim_catalogue_item_ids'));
        $images = $request->file('images', []);

        $saveClaimRequestDtos = [];
        foreach ($claimCatalogueItems as $claimCatalogueItem) {
            $saveClaimRequestDtos[] = new SaveClaimRequestDto(
                claimCatalogueItems: $claimCatalogueItem,
                comment: $request->input('comment'),
                arrivalDate: $request->input('arrival_date') ?
                    Carbon::parse($request->input('arrival_date')) : null,
                imageDtos: null,
                theme: $this->getType($request->input('theme_id'))
            );
        }

        try {
            $claimId = $this->claimService->saveCreate($accountNumber, $this->getAuthUser(), $saveClaimRequestDtos);
            $this->claimService->uploadImages($images, $this->getAuthUser(), $claimId);
        } catch (BadRequestException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e, $e->getCode());
        } catch (NotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e, $e->getCode());
        }

        return $this->empty();
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws AuthenticationException
     * @throws InvalidArgumentException
     */
    public function storeAppeal(string $accountNumber, SaveClaimAppealRequest $request): Response
    {
        $claimCatalogueItems = $this->splitCatalogueItemsByParent($request->input('claim_catalogue_item_ids'));

        $saveClaimAppealDtos = [];
        foreach ($claimCatalogueItems as $claimCatalogueItem) {
            $saveClaimAppealDtos[] = new SaveClaimAppealDto(
                claimCatalogueItems: $claimCatalogueItem,
                comment: $request->input('comment'),
                imageDtos: $this->getImageDtos($request->file('images', [])),
            );
        }

        $this->claimService->saveAppeal($accountNumber, $this->getAuthUser(), $saveClaimAppealDtos);

        return $this->empty();
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws AuthenticationException
     * @throws InvalidArgumentException
     */
    public function storeVisit(string $accountNumber, SaveClaimVisitRequest $request): Response
    {
        $claimCatalogueItem = $this->findClaimCatalogueItem($request->input('claim_catalogue_item_id'));

        $dto = new SaveClaimVisitDto(
            claimCatalogueItem: $claimCatalogueItem,
            arrivalDate: Carbon::parse($request->input('arrival_date')),
            comment: $request->input('comment'),
            imageDtos: $this->getImageDtos($request->file('images', [])),
        );
        $this->claimService->saveVisit($accountNumber, $this->getAuthUser(), $dto);

        return $this->empty();
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws AuthenticationException
     * @throws InvalidArgumentException
     */
    public function storeWarranty(string $accountNumber, SaveClaimWarrantyRequest $request): Response
    {
        $claimCatalogueItems = $this->splitCatalogueItemsByParent($request->input('claim_catalogue_item_ids'));

        $saveClaimDtos = [];
        foreach ($claimCatalogueItems as $claimCatalogueItem) {
            $saveClaimDtos[] = new SaveClaimWarrantyDto(
                claimCatalogueItems: $claimCatalogueItem,
                comment: $request->input('comment'),
                arrivalDate: $request->input('arrival_date') ?
                    Carbon::parse($request->input('arrival_date')) : null,
                imageDtos: $this->getImageDtos($request->file('images', [])),
            );
        }

        $this->claimService->saveWarranty($accountNumber, $this->getAuthUser(), $saveClaimDtos);

        return $this->empty();
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws AuthenticationException
     * @throws InvalidArgumentException
     */
    public function storeMarketplace(string $accountNumber, SaveClaimMarketplaceRequest $request): Response
    {
        $claimsArray = $this->claimService->groupClaimsForMarketplace(
            $request->input('claim_catalogue_items')
        );

        foreach ($claimsArray as $claims) {
            $dto = new SaveClaimMarketplaceDto(
                claimMarketplaceItemDtos: $claims,
                // all date fields in CRM are shifted by 3 hours earlier
                arrivalDate: $request->input('arrival_date') ?
                    Carbon::parse($request->input('arrival_date'))->subHours(3) : null,
                imageDtos: $this->getImageDtos($request->file('images', [])),
                comment:  $request->input('comment') ?? null
            );
            $this->claimService->saveMarketplace($accountNumber, $this->getAuthUser(), $dto);
        }

        return $this->empty();
    }

    public function attachment(string $claim_id)
    {
        $claim = $this->claimService->getClaimFiles($claim_id);

        if ($claim == null) {
            return $this->empty();
        }

        return response()->json(new ClaimAttachmentCollection($claim['documentList']));
    }

    public function saveAttachment(string $claim_id, SaveClaimAttachmentRequest $request)
    {
        $file = $request->input('file_body');

        $types = ['image/jpg', 'image/jpeg', 'image/png', 'image/bmp', 'image/webp'];
        if (in_array($request->input('mime_type'), $types)) {
            $image = $this->fromBase64(base64_decode($file));
            $input['file'] = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path();

            $imgFile = Image::make($image->getRealPath())->save($destinationPath.'/'.$input['file'], 20)
                ->encode('data-url', 20);
            $img = $imgFile->encoded;
            $img = preg_replace('#^data:image/[^;]+;base64,#', '', $img);
        } else {
            $img = $file;
        }

        $dto = new SaveClaimAttachmentDto(
            file_name: $request->input('file_name'),
            file_body: $img,
            mime_type: $request->input('mime_type'),
            document_type_code: $request->input('document_type_code'),
            document_type_name: $request->input('document_type_name'),
            claim_id: $claim_id,
            crm_user_id: $this->getAuthUser()->crm_id
        );

        $this->claimService->saveAttachmentFiles($claim_id, $this->getAuthUser(), $dto);

        $path = storage_path('logs/downloaded_files.log');
        $text = date('Y-m-d H:i:s').' | request: '. $_SERVER['REQUEST_URI']. " | " .
            $dto->file_name . " | " . $dto->mime_type . " | " . $dto->crm_user_id;

        file_put_contents($path, PHP_EOL . $text, FILE_APPEND);

        return $this->response()->setStatusCode(200);
    }

    public function getAttachmentById(string $claim_id, string $document_id)
    {
        $document = $this->claimService->getClaimFileById($claim_id, $document_id);


        if (!$document) {
            throw new NotFoundHttpException('Document not found.');
        }

        return response($document->getBody());
    }

    private function findClaim(string $id): Claim
    {
        $claim = $this->repository->getOneById($id);

        if ($claim === null) {
            throw new NotFoundHttpException('Claim not found.');
        }

        return $claim;
    }

    /**
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    private function findClaimCatalogueItem(string $id): ClaimCatalogueItem
    {
        $claimCatalogueItem = $this->catalogueRepository->getOneById($id);
        if ($claimCatalogueItem === null) {
            throw new NotFoundHttpException('Claim Catalogue Item not found.');
        }

        return $claimCatalogueItem;
    }

    /**
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    private function findAllClaimCatalogueItem()
    {
        $claimCatalogueItem = $this->catalogueRepository->getAll();
        if ($claimCatalogueItem === null) {
            throw new NotFoundHttpException('Claim Catalogue Item not found.');
        }

        return $claimCatalogueItem;
    }

    /**
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    private function findAccountCatalogueItem(string $accountNumber, string $id): ClaimCatalogueItem
    {
        $claimCatalogueItem = $this->catalogueRepository->getAccountOneById($accountNumber, $id);
        if ($claimCatalogueItem === null) {
            throw new NotFoundHttpException('Claim Catalogue Item not found.');
        }

        return $claimCatalogueItem;
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws InvalidArgumentException
     */
    private function splitCatalogueItemsByParent(array $catalogueItemIds): array
    {
        $claimCatalogueItems = [];
        foreach ($catalogueItemIds as $item) {
            $catalogueItem = $this->findClaimCatalogueItem($item);

            $catalogueItemParent = null;
            if ($catalogueItem->getParentId()) {
                $catalogueItemParent = $this->findClaimCatalogueItem($catalogueItem->getParentId());
            }

            $claimCatalogueItems[$catalogueItemParent?->getId() ?? $catalogueItem->getId()][] = $catalogueItem;
        }

        return $claimCatalogueItems;
    }

    public function getType(string $id): array
    {
        $types = [
            1 => 'Оставить заявку',
            4 => 'Маркетплейс',
            5 => 'Задать вопрос',
            6 => 'Обращение',
            7 => 'Запрос справок/документов',
            8 => 'Записаться на  прием',
            10 => 'Заказ пропуска',
            11 => 'АВАРИЙНАЯ',
            12 => 'Гарантия',
        ];

        return [
            'code' => $id,
            'name' => $types[$id],
        ];
    }

    private function getImageDtos(array $uploadedFiles): array
    {
        $imageDtos = [];
        foreach ($uploadedFiles as $uploadedFile) {
            $types = ['image/jpg', 'image/jpeg', 'image/png', 'image/bmp', 'image/webp'];

            if (in_array($uploadedFile->getClientMimeType(), $types)) {
                $file = $uploadedFile->getContent();
                $image = $this->fromBase64($file);
                $input['file'] = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path();

                $imgFile = Image::make($image->getRealPath())->save($destinationPath.'/'.$input['file'], 20)
                    ->encode('data-url', 20);
                $img = $imgFile->encoded;
                $img = file_get_contents($img);
            } else {
                $img = $uploadedFile->getContent();
            }

            $imageDtos[] = new ClaimImageDto(
                name: pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME),
                fileName: $uploadedFile->getClientOriginalName(),
                documentType: DocumentType::photoFromApplicant(),
                documentSubtype: null,
                isCustomerAvailable: true,
                documentBody: $img,
                mimeType: $uploadedFile->getClientMimeType(),
                sender: $this->getAuthUser()->crm_id,
            );

            $path = storage_path('logs/downloaded_files.log');
            $text = date('Y-m-d H:i:s').' | request: '. $_SERVER['REQUEST_URI']. " | " .
                $uploadedFile->getClientOriginalName() . " | " . $uploadedFile->getClientMimeType() .
                " | " . $this->getAuthUser()->crm_id;

            file_put_contents($path, PHP_EOL . $text, FILE_APPEND);
        }

        return $imageDtos;
    }


    public static function fromBase64(string $base64File): UploadedFile
    {
        // save it to temporary dir first.
        $tmpFilePath = sys_get_temp_dir() . '/' . Str::uuid()->toString();
        file_put_contents($tmpFilePath, $base64File);

// this just to help us get file info.
        $tmpFile = new File($tmpFilePath);

        $file = new UploadedFile(
            $tmpFile->getPathname(),
            $tmpFile->getFilename(),
            $tmpFile->getMimeType(),
            0,
            true // Mark it as test, since the file isn't from real HTTP POST.
        );

        return $file;
    }
}
