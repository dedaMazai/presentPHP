<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Http\Api\External\V1\Requests\LegalPerson\AddAccountToDemandRequest;
use App\Http\Api\External\V1\Requests\LegalPerson\UpdateLegalPersonRequest;
use App\Http\Resources\LegalPerson\AccountAddResource;
use App\Http\Resources\LegalPerson\AccountFillResource;
use App\Http\Resources\LegalPerson\AccountResource;
use App\Http\Resources\LegalPerson\CheckInnResource;
use App\Services\LegalPerson\LegalPersonService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LegalPersonController
 *
 * @package App\Http\Api\External\V1\Controllers
 */
class LegalPersonController extends Controller
{
    public function __construct(private LegalPersonService $service)
    {
    }

    public function checkInn(int $inn): Response
    {
        try {
            $dto = $this->service->checkInn($inn);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
        return response()->json(new AccountResource($dto));
    }

    public function update($id, UpdateLegalPersonRequest $request): Response
    {
        $dto = $this->service->updateLegalPerson($id, $request);
        return response()->json(new AccountResource($dto));
    }

    public function accountFill(int $inn): Response
    {
        $dto = $this->service->fill($inn);
        return response()->json(new AccountFillResource($dto));
    }

    public function addAccount(string $id, AddAccountToDemandRequest $request): Response
    {
        $userId = $this->getAuthUser()->crm_id;
        $demand = $this->service->addAccountToDemand($id, $request->validated(), $userId);
        return response()->json(new AccountAddResource($demand));
    }
}
