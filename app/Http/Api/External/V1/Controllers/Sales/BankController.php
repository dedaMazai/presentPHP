<?php

namespace App\Http\Api\External\V1\Controllers\Sales;

use App\Http\Resources\Sales\Bank\BankCollection;
use App\Models\Sales\Bank\BankType;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\Sales\BankRepository;
use App\Services\Sales\Demand\DemandRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BankController
 *
 * @package App\Http\Api\External\V1\Controllers\Sales
 */
class BankController extends BaseSalesController
{
    public function __construct(
        private BankRepository $bankRepository,
        private DemandRepository $demandRepository,
    ) {
        parent::__construct($this->demandRepository);
    }

    /**
     * @throws ValidationException
     * @throws BadRequestException
     */
    public function index(string $demandId, Request $request): Response
    {
        $this->validate($request, [
            'type' => [
                'nullable',
                Rule::in(BankType::toValues()),
            ],
        ]);

        $banks = $this->bankRepository->getAllByAddressId(
            $this->findDemand($demandId)->getProperty()->getAddress()->getId(),
            BankType::tryFrom($request->input('type') ?? '')
        );

        return response()->json(new BankCollection($banks));
    }
}
