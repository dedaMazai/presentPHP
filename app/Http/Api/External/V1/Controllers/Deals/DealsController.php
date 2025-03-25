<?php

namespace App\Http\Api\External\V1\Controllers\Deals;

use App\Http\Api\External\V1\Controllers\Sales\BaseSalesController;
use App\Http\Api\External\V1\Requests\Deal\SetDepositorRequest;
use App\Http\Api\External\V1\Requests\Sales\SetBankIdRequest;
use App\Http\Resources\Deal\FinishingCollection;
use App\Http\Resources\Deal\InstallmetResource;
use App\Http\Resources\Deal\PaymentTypeResource;
use App\Models\Sales\Bank\DemandBankType;
use App\Models\Sales\PaymentMode;
use App\Services\Deal\DemandDealRepository;
use App\Services\Deal\DemandDealService;
use App\Services\Deal\Dto\SetBankDto;
use App\Services\Deal\Exceptions\ChangingTypeOfPaymenNotAllowedException;
use App\Services\Deal\Exceptions\FinishHasAlreadyException;
use App\Services\Deal\Exceptions\FinishingCannotBeAppliedException;
use App\Services\Deal\Exceptions\InvalidPaymentTypeException;
use App\Services\Deal\Exceptions\MissingPaymentModeCodeException;
use App\Services\Deal\Exceptions\NotAllowedFinishingForThisObjectException;
use App\Services\Deal\Exceptions\PeriodOfFinishingNotDefinedException;
use App\Services\Sales\Demand\DemandRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Illuminate\Validation\ValidationException;

/**
 * Class DealsController
 *
 * @package App\Http\Api\External\V1\Controllers\Deals
 */
class DealsController extends BaseSalesController
{
    public function __construct(
        private DemandDealService $demandDealService,
        private DemandRepository $demandRepository,
        private DemandDealRepository $demandDealRepository
    ) {
        parent::__construct($this->demandRepository);
    }

    /**
     * @param string $demandId
     * @return Response
     * @throws \App\Services\DynamicsCrm\Exceptions\BadRequestException
     * @throws \App\Services\DynamicsCrm\Exceptions\NotFoundException
     */
    public function index(string $demandId): Response
    {
        $demand = $this->findDemand($demandId);

        $finishings = $this->demandDealService->getFinishing($demand);

        if ($finishings == []) {
            return $this->empty();
        }

        return response()->json(new FinishingCollection($finishings));
    }

    /**
     * @param string $demandId
     * @param string $finishId
     * @return Response
     * @throws FinishHasAlreadyException
     * @throws FinishingCannotBeAppliedException
     * @throws PeriodOfFinishingNotDefinedException
     * @throws \App\Services\DynamicsCrm\Exceptions\BadRequestException
     * @throws \App\Services\DynamicsCrm\Exceptions\NotFoundException
     */
    public function addFinishVariant(string $demandId, string $finishId): Response
    {
        $demand = $this->findDemand($demandId);

        // phpcs:disable
        try {
            $this->demandDealService->addFinishVariant($demand, $finishId);
        } catch (FinishHasAlreadyException|PeriodOfFinishingNotDefinedException|FinishingCannotBeAppliedException|NotAllowedFinishingForThisObjectException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], $e->getCode());
        }
        // phpcs:enable

        return $this->empty();
    }

    /**
     * @throws MissingPaymentModeCodeException
     * @throws \Illuminate\Auth\AuthenticationException
     * @throws ChangingTypeOfPaymenNotAllowedException
     * @throws InvalidPaymentTypeException
     */
    public function setPaymentType(string $demandId, string $paymentType): Response
    {
        if (PaymentMode::from($paymentType)??null === PaymentMode::mortgage() ??
            PaymentMode::from($paymentType)??null === PaymentMode::full() ??
            PaymentMode::from($paymentType)??null === PaymentMode::instalment()
            ) {
            $demand = $this->findDemand($demandId);

            $this->demandDealService->setPaymentType($demand, $paymentType, $this->getAuthUser());
        } else {
            throw new InvalidPaymentTypeException();
        }

        $demand = $this->findDemand($demandId);

        return response()->json(new PaymentTypeResource($demand));
    }

    /**
     * @param string $demandId
     * @param SetBankIdRequest $request
     * @return Response
     * @throws \App\Services\DynamicsCrm\Exceptions\BadRequestException
     * @throws \App\Services\DynamicsCrm\Exceptions\NotFoundException
     */
    public function setBankId(string $demandId, SetBankIdRequest $request): Response
    {
        $bankDto = new SetBankDto(
            demandId: $demandId,
            bankId: $request->bank_id,
            bankType: DemandBankType::from($request->bank_type),
            isSberClient: $request->is_sber_client??null
        );

        $message = $this->demandDealRepository->setBankId($bankDto, $this->getAuthUser());

        return response()->json(['message' => $message]);
    }

    /**
     * @param string $demandId
     * @param string $installmetId
     * @return Response
     * @throws ChangingTypeOfPaymenNotAllowedException
     * @throws MissingPaymentModeCodeException
     */
    public function setInstallmet(string $demandId, string $installmetId): Response
    {
        $demand = $this->findDemand($demandId);

        $this->demandDealService->setInstallmet($demand, $installmetId);

        $demand = $this->findDemand($demandId);

        return $this->empty();
    }

    /**
     * @param string $demandId
     * @return Response
     * @throws \App\Services\DynamicsCrm\Exceptions\BadRequestException
     * @throws \App\Services\DynamicsCrm\Exceptions\NotFoundException
     */
    public function cancelDemand(string $demandId): Response
    {
        $demand = $this->findDemand($demandId);

        $this->demandDealRepository->clearArticleId($demandId);

        return $this->response();
    }

    /**
     * @param string $demandId
     * @param SetDepositorRequest $request
     * @return Response
     */
    public function setDepositor(string $demandId, SetDepositorRequest $request): Response
    {
        $this->demandDealService->setDepositor($demandId, $request);

        return $this->empty();
    }
}
