<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Http\Resources\InstructionCollection;
use App\Http\Resources\InstructionResource;
use App\Http\Resources\Pass\PassCollection;
use App\Models\Instruction\Instruction;
use App\Models\Pass\Pass;
use App\Models\Pass\PassAssignment;
use App\Models\Pass\PassCarType;
use App\Models\Pass\PassType;
use App\Services\Pass\Dto\SavePassDto;
use App\Services\Pass\PassRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PassController
 *
 * @package App\Http\Api\External\V1\Controllers
 */
class PassController extends Controller
{
    public function __construct(private PassRepository $repository)
    {
    }

    public function index(string $accountNumber, Request $request): Response
    {
        $this->validate($request, [
            'pass_type' => [
                Rule::in(['onetime', 'permanent']),
            ],
            'sort' => [
                'nullable',
            ],
            'page' => [
                'nullable'
            ]
        ]);

        $passes = $this->repository->getPassesByAccountNumber(
            $accountNumber,
            $request->input('pass_type'),
            $request->input('sort')
        );

        return response()->json(new PassCollection($this->paginate($passes)));
    }

    public function store(string $accountNumber, Request $request)
    {
        $this->validate($request, [
            'pass_type' => [
                'required',
                Rule::in(PassType::toValues()),
            ],
            'assignment' => [
                'required',
                Rule::in(PassAssignment::toValues()),
            ],
            'car_type' => [
                Rule::in(PassCarType::toValues()),
            ],
        ]);

        $passDto = new SavePassDto(
            passType: $request->has('pass_type')?PassType::from($request->input('pass_type')):null,
            assignment: $request->has('assignment')?PassAssignment::from($request->input('assignment')):null,
            carType: $request->has('car_type')?PassCarType::from($request->input('car_type')):null,
            carNumber: $request->input('car_number'),
            name: $request->input('name'),
            arrivalDate: $request->input('arrival_date')?Carbon::parse($request->input('arrival_date')):null,
            startDate: $request->input('start_date')?Carbon::parse($request->input('start_date')):null,
            endDate: $request->input('start_date')?Carbon::parse($request->input('end_date')):null,
            comment: $request->input('comment'),
        );

        $this->repository->storePass($accountNumber, $passDto);

        return $this->response()->setStatusCode(200);
    }

    public function cancel(string $passId)
    {
        $this->repository->cancelPass($passId);

        return $this->empty();
    }

    public function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
