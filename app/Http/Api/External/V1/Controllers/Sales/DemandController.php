<?php

namespace App\Http\Api\External\V1\Controllers\Sales;

use App\Http\Api\External\V1\Requests\Demand\UpdateDemandConfidantRequest;
use App\Http\Api\External\V1\Requests\Sales\CreateDemandRequest;
use App\Http\Api\External\V1\Requests\Sales\DeponentRequest;
use App\Http\Api\External\V1\Requests\Sales\HypothecRequest;
use App\Http\Api\External\V1\Requests\Sales\SendContractDraftRequest;
use App\Http\Api\External\V1\Requests\Sales\SetJointOwnersRequest;
use App\Http\Api\External\V1\Requests\Sales\SetMeetingRequest;
use App\Http\Api\External\V1\Requests\Sales\TradeInRequest;
use App\Http\Resources\Sales\Borrower\BorrowerColection;
use App\Http\Resources\Sales\Demand\DemandCollection;
use App\Http\Resources\Sales\Demand\DemandResource;
use App\Http\Resources\Sales\Demand\DetailDemandResource;
use App\Http\Resources\Sales\Demand\Payment\PaymentByDefaultResource;
use App\Http\Resources\Sales\Demand\PaymentPlan\DemandPaymentPlanCollection;
use App\Http\Resources\Sales\Deponent\DeponentResource;
use App\Http\Resources\Sales\Hypothec\ApprovalsCollection;
use App\Http\Resources\Sales\Hypothec\ApprovalsResource;
use App\Http\Resources\Sales\Hypothec\HypothecBankApprovalsResource;
use App\Http\Resources\Sales\Hypothec\Info\HypothecInfoResource;
use App\Http\Resources\Sales\Hypothec\Info\StoreHypothecResource;
use App\Models\Document\DocumentSubtype;
use App\Models\Document\DocumentType;
use App\Models\Role;
use App\Models\Sales\Demand\Demand;
use App\Models\Sales\FamilyStatus;
use App\Models\Sales\OwnerObjectType;
use App\Models\Sales\OwnerType;
use App\Models\Sales\PaymentMode;
use App\Models\Sales\Property\PropertyStatus;
use App\Models\Sales\StepMapper;
use App\Services\Contract\ContractRepository;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\Sales\Deal\DealService;
use App\Services\Sales\Demand\DemandRepository;
use App\Services\Sales\Demand\DemandService;
use App\Services\Sales\Demand\Dto\CreateJointOwnerDto;
use App\Services\Sales\Demand\Dto\CreateJointOwnerMeetingDto;
use App\Services\Sales\Demand\Dto\JointOwnerDocumentDto;
use App\Services\Sales\Demand\Dto\JointOwnerDto;
use App\Services\Sales\Demand\Dto\JointOwnerMeetingDto;
use App\Services\Sales\Demand\Dto\MortgageTermsDto;
use App\Services\Sales\Demand\Dto\SetTermsDto;
use App\Services\Sales\Demand\Exceptions\TooManyBookingAttemptsException;
use App\Services\Sales\Property\PropertyRepository;
use Carbon\Carbon;
use http\Exception\InvalidArgumentException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class DemandController
 *
 * @package App\Http\Api\External\V1\Controllers\Sales
 */
class DemandController extends BaseSalesController
{
    public function __construct(
        private PropertyRepository $propertyRepository,
        private DemandRepository $demandRepository,
        private ContractRepository $contractRepository,
        private DemandService $demandService,
        private DealService $dealService,
    ) {
        parent::__construct($this->demandRepository);
    }

    /**
     * @throws AuthenticationException
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function index(): Response
    {
        $demands = $this->demandRepository->getDemands($this->getAuthUser());

        if ($demands == []) {
            return $this->empty();
        }

        return response()->json(new DemandCollection($demands));
    }

    public function show(string $id): Response
    {
        $demand = $this->findDemand($id);

        return response()->json(new DetailDemandResource($demand));
    }

    /**
     * @throws BadRequestException
     * @throws AuthenticationException
     * @throws NotFoundException
     */
    public function store(CreateDemandRequest $request): Response
    {
        try {
            $propertyBookingDto = $this->propertyRepository->getPropertyForBooking($request->input('property_id'));
        } catch (NotFoundException) {
            throw new NotFoundHttpException('Property not found.');
        }
        if (!$propertyBookingDto->status->equals(PropertyStatus::free())) {
            throw new AccessDeniedHttpException('Property are not available.');
        }

        try {
            $demandId = $this->demandService->createDemand($propertyBookingDto, $this->getAuthUser());
        } catch (TooManyBookingAttemptsException) {
            throw new HttpException(429, 'Too many booking attempts.');
        }

        return $this->response(['demand_id' => $demandId]);
    }

    public function getSteps(string $id): Response
    {
        $demand = $this->findDemand($id);

        return $this->response(StepMapper::getForDemand($demand));
    }

    public function storeTradeIn(TradeInRequest $request): Response
    {
        $this->demandService->tradeInRequest($request);

        return $this->response();
    }

    public function sendContractDraft(SendContractDraftRequest $request): Response
    {
        $userCrmId = $this->getAuthUser()->crm_id;
        $this->demandService->setContractDraft($request->demand_id, $userCrmId);

        return $this->response();
    }

    public function getDeponent(DeponentRequest $request): Response
    {
        $id = $request->get('id');
        $type = $request->get('type');
        $deponent = [];

        if ($type == 'demand') {
            $object = $this->findDemand($id);
        } elseif ($type == 'contract') {
            $object = $this->contractRepository->getById($id);
        }

        if (isset($object)) {
            $deponent = $this->demandService->getDeponent($object, $id);
        }

        return response()->json(new DeponentResource([
            'depositor_info' => $deponent['depositorInfo'] ?? null,
            'depositor_document' => $deponent['depositorDocument'],
            'owner_id' => $deponent['ownerId'],
        ]));
    }

    public function getBorrowers(string $id): Response
    {
        $demand = $this->findDemand($id);
        $borrowers = [];

        $borrowers = $this->demandService->getBorrowers($demand);

        return response()->json(new BorrowerColection($borrowers));
    }

    public function getHypothecInfo(string $id): Response
    {
        $user = $this->getAuthUser();
        $hypothecInfo = $this->demandService->hypothecInfo($user, $id);

        return response()->json(new HypothecInfoResource($hypothecInfo));
    }

    public function getPaymentPlan(string $id): Response
    {
        $demand = $this->findDemand($id);

        return response()->json(new DemandPaymentPlanCollection($demand->getPaymentPlan()));
    }

    public function setJointOwnersByDefault(string $demandId): Response
    {
        $demand = $this->findDemand($demandId);
        $customerId = $this->getAuthUser()->crm_id;

        $this->demandService->setJointOwnerByDefault($demand, $customerId);

        return $this->response();
    }

    public function setPaymentByDefault(string $demandId): Response
    {
        $demand = $this->findDemand($demandId);
        $user = $this->getAuthUser();
        $this->demandService->setPaymentByDefault($demand, $user);
        $demand = $this->findDemand($demandId);

        return response()->json(new PaymentByDefaultResource($demand));
    }

    /**
     * @throws AuthenticationException
     * @throws ValidationException
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function setTerms(string $id, SetJointOwnersRequest $request): Response
    {
        $demand = $this->findDemand($id);

        $this->validateTerms($demand, $request);

        $mortgageTermsDto = new MortgageTermsDto(
            isDigital: $request->input('is_digital_mortgage'),
            approvalId: $request->input('mortgage_approval_id'),
            bankId: $request->input('mortgage_bank_id'),
            approvalDate: $request->input('mortgage_approval_date') ?
                new Carbon($request->input('mortgage_approval_date')) : null,
            isManagerNotExist: $request->input('is_mortgage_manager_not_exist'),
            managerFio: $request->input('mortgage_manager_fio'),
            managerPhone: $request->input('mortgage_manager_phone'),
        );
        $setTermsDto = new SetTermsDto(
            demand: $demand,
            paymentMode: PaymentMode::from($request->input('payment_mode')),
            letterOfCreditBankId: $request->input('bank_id'),
            isEscrowBankClient: $request->input('is_escrow_bank_client'),
            instalmentId: $request->input('instalment_id'),
            mortgageTerms: $mortgageTermsDto,
        );
        $this->demandService->setTerms($demand, $setTermsDto);

        $jointOwners = [];
        foreach ($request->input('owners') as $key => $owner) {
            $existJointOwner = null;

            $ownerObjectType = OwnerObjectType::from($owner['owner_object_type']);
            if (OwnerObjectType::myself()->equals($ownerObjectType)) {
                foreach ($demand->getJointOwners() as $demandJointOwner) {
                    if ($demandJointOwner->getId() == strtolower($this->getAuthUser()->crm_id)) {
                        $existJointOwner = $demandJointOwner;

                        break;
                    }
                }

                if (!$existJointOwner) {
                    throw new NotFoundHttpException('Joint Owner not found.');
                }
            }

            $documentDtos = [];
            /** @var UploadedFile $file */
            foreach ($request->file('owners')[$key] as $documentParentType => $filesParentType) {
                foreach ($filesParentType as $documentType => $filesType) {
                    foreach ($filesType as $file) {
                        $documentDtos[] = new JointOwnerDocumentDto(
                            name: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                            fileName: $file->getClientOriginalName(),
                            type: DocumentType::from(strval($documentParentType)),
                            subtype: DocumentSubtype::tryFrom(strval($documentType)),
                            isCustomerAvailable: true,
                            documentBody: base64_encode($file->getContent()),
                            mimeType: $file->getClientMimeType(),
                        );
                    }
                }
            }

            $familyStatus = FamilyStatus::tryFrom($owner['family_status'] ?? '') ?? FamilyStatus::single();

            $ownerType = OwnerType::from($request->input('owner_type'));
            if (OwnerType::personal()->equals($ownerType)) {
                $part = '1/1';
            } elseif (OwnerType::joint()->equals($ownerType)) {
                $part = '1/2';
                $familyStatus = FamilyStatus::married();
            } else {
                $part = $owner['part'];
            }

            $jointOwners[] = new JointOwnerDto(
                jointOwnerId: $existJointOwner?->getJointOwnerId(),
                customerId: $existJointOwner?->getId() ?? $owner['customer_id'] ?? null,
                lastName: $existJointOwner?->getLastName() ?? $owner['last_name'],
                firstName: $existJointOwner?->getFirstName() ?? $owner['first_name'],
                middleName: $existJointOwner?->getMiddleName() ?? $owner['middle_name'],
                phone: $existJointOwner?->getPhone() ?? $owner['phone'],
                birthDate: $existJointOwner?->getBirthDate() ?? new Carbon($owner['birth_date']),
                email: $existJointOwner?->getEmail() ?? $owner['phone'],
                inn: $owner['inn'],
                citizenship: $owner['citizenship'] ?? null,
                familyStatus: $familyStatus,
                part: $part,
                ownerType: $ownerType,
                role: Role::client(),
                documents: $documentDtos,
            );
        }
        $createJointOwnerDto = new CreateJointOwnerDto(
            demandId: $demand->getId(),
            jointOwners: $jointOwners,
        );

        $this->demandService->setJointOwners($createJointOwnerDto);

        $this->dealService->setCurrentStep($demand->getDeal(), StepMapper::STEP_CONTRACT);

        return $this->empty();
    }

    /**
     * @throws BadRequestException
     * @throws ValidationException
     * @throws NotFoundException
     */
    public function setFinishing(string $id, Request $request): Response
    {
        $this->validate($request, [
            'finishing_id' => 'required|string',
        ]);

        $demand = $this->findDemand($id);
        if (!$demand->isFinishingAvailable()) {
            throw new AccessDeniedHttpException('Finishing select period is end.');
        }

        $this->demandService->setFinishing(
            $demand->getId(),
            $request->input('finishing_id'),
        );

        return $this->empty();
    }

    /**
     * @throws BadRequestException
     * @throws ValidationException
     * @throws NotFoundException
     */
    public function storeHypothec(HypothecRequest $request, $demandId): Response
    {
        try {
            $hypothec = $this->demandService->storeHypothec($request, $demandId, $this->getAuthUser());
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }

        return response()->json($hypothec);
    }

    /**
     * @throws BadRequestException
     * @throws ValidationException
     * @throws NotFoundException
     */
    public function getHypothec(string $demandId): Response
    {
        $hypothec = $this->demandService->getHypothec($demandId, $this->getAuthUser());

        return response()->json($hypothec);
    }

    public function getHypothecApprovals(string $demandId): Response
    {
        $approvals = $this->demandService->getApprovals($demandId);

        return response()->json(new ApprovalsCollection($approvals));
    }

    public function getHypothecBankApprovals(string $approvalId): Response
    {
        $approvals = $this->demandService->getBankApprovals($approvalId);

        return response()->json(new HypothecBankApprovalsResource($approvals));
    }

    public function setContractRead(string $id): Response
    {
        $demand = $this->findDemand($id);
        if ($demand->getDeal()->contract_read_at) {
            throw new BadRequestHttpException('Contract already read.');
        }

        $this->dealService->setContractReadAt(
            $demand->getDeal(),
            new Carbon(),
        );

        $this->dealService->setCurrentStep($demand->getDeal(), StepMapper::STEP_PREPARE_SIGN);

        return $this->empty();
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function setMeeting(string $id, SetMeetingRequest $request): Response
    {
        $demand = $this->findDemand($id);

        $jointOwnerMeetings = [];
        foreach ($request->input('owners') as $owner) {
            foreach ($demand->getJointOwners() as $demandJointOwner) {
                if ($demandJointOwner->getId() == strtolower($owner['joint_owner_id'])) {
                    $jointOwnerMeetings[] = new JointOwnerMeetingDto(
                        id: $demandJointOwner->getJointOwnerId(),
                        part: $demandJointOwner->getPart(),
                        role: $demandJointOwner->getRole(),
                        ownerType: $demandJointOwner->getOwnerType(),
                        ownerTypeComment: $demandJointOwner->getOwnerTypeComment(),
                        addressCourier: $owner['address'],
                        meetingDate: Carbon::now(),
                    );

                    break;
                }
            }
        }
        $createJointOwnerMeetingsDto = new CreateJointOwnerMeetingDto(
            demandId: $demand->getId(),
            jointOwnerMeetings: $jointOwnerMeetings,
        );

        $this->demandService->setJointOwnerMeetings($createJointOwnerMeetingsDto);

        return $this->empty();
    }

    /**
     * @throws ValidationException
     */
    private function validateTerms(Demand $demand, SetJointOwnersRequest $request): void
    {
        $paymentMode = PaymentMode::from($request->input('payment_mode'));
        $isMortgage = $paymentMode->equals(PaymentMode::mortgage());
        $isDigitalMortgage = $request->input('is_digital_mortgage', false);
        $isMortgageManagerNotExist = $request->input('is_mortgage_manager_not_exist', false);

        $this->validate($request, [
            'bank_id' => [
                'string',
                Rule::requiredIf(function () use ($demand) {
                    return $demand->getIsLetterOfCredit() ?? false;
                }),
            ],
            'is_escrow_bank_client' => [
                'boolean',
                Rule::requiredIf(function () use ($demand) {
                    return $demand->getProperty()->getIsEscrow();
                }),
            ],
            'payment_mode' => [
                'required',
                Rule::in(PaymentMode::toValues()),
            ],
            'instalment_id' => [
                'string',
                Rule::requiredIf(function () use ($paymentMode) {
                    return $paymentMode->equals(PaymentMode::instalment());
                }),
            ],
            'is_digital_mortgage' => [
                'boolean',
                Rule::requiredIf(function () use ($isMortgage) {
                    return $isMortgage;
                }),
            ],
            'mortgage_approval_id' => [
                'string',
                Rule::requiredIf(function () use ($isDigitalMortgage) {
                    return $isDigitalMortgage;
                }),
            ],
            'mortgage_bank_id' => [
                'string',
                Rule::requiredIf(function () use ($isMortgage, $isDigitalMortgage) {
                    return $isMortgage && !$isDigitalMortgage;
                }),
            ],
            'mortgage_approval_date' => [
                'date',
                Rule::requiredIf(function () use ($isMortgage, $isDigitalMortgage) {
                    return $isMortgage && !$isDigitalMortgage;
                }),
            ],
            'is_mortgage_manager_not_exist' => [
                'boolean',
                Rule::requiredIf(function () use ($isMortgage, $isDigitalMortgage) {
                    return $isMortgage && !$isDigitalMortgage;
                }),
            ],
            'mortgage_manager_fio' => [
                'string',
                Rule::requiredIf(function () use ($isMortgage, $isDigitalMortgage, $isMortgageManagerNotExist) {
                    return $isMortgage && !$isDigitalMortgage && !$isMortgageManagerNotExist;
                }),
            ],
            'mortgage_manager_phone' => [
                'string',
                Rule::requiredIf(function () use ($isMortgage, $isDigitalMortgage, $isMortgageManagerNotExist) {
                    return $isMortgage && !$isDigitalMortgage && !$isMortgageManagerNotExist;
                }),
            ],
        ]);
    }
    public function confidantEdit(string $demandId, UpdateDemandConfidantRequest $request): Response
    {
        $confidantInfo = $this->demandService->updateConfidant(
            $demandId,
            $this->getAuthUser(),
            $request->validated()
        );

        return response()->json(['confidant_id' => $confidantInfo['primaryContact']['code']], 200);
    }
}
