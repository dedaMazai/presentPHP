<?php

namespace App\Http\Api\External\V2\Controllers;

use App\Http\Api\External\V2\Requests\SaveRelationshipInviteRequest;
use App\Http\Resources\V2\RelationshipInvite\RelationshipInviteCollection;
use App\Models\Role;
use App\Services\Account\AccountRepository;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\RelationshipInvite\Dto\SaveRelationshipInviteDto;
use App\Services\V2\RelationshipInvite\Exception\UserHasInvationException;
use App\Services\V2\RelationshipInvite\RelationshipInviteService;
use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RelationshipInviteController
 *
 * @package App\Http\Api\External\V2\Controllers
 */
class RelationshipInviteController extends Controller
{
    public function __construct(
        private readonly RelationshipInviteService $service,
        private readonly AccountRepository $repository
    ) {
    }

    /**
     * @throws BadRequestException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws ValidationException
     * @throws InvalidArgumentException
     */
    public function store(
        string $accountNumber,
        SaveRelationshipInviteRequest $request,
    ): Response {
        Gate::authorize("manipulate-relationship", $accountNumber);

        try {
            $this->service->store($this->getAuthUser(), $accountNumber, $request);
        } catch (UserHasInvationException $e) {
            return response()->json([
                "message" => $e->getMessage()
            ], $e->getCode());
        }

        $account = $this->repository->getAccountByNumber($accountNumber);

        $saveRelationshipInviteDto = new SaveRelationshipInviteDto(
            firstName: $request->input("first_name"),
            lastName: $request->input("last_name"),
            phone: $this->formatPhoneNumber($request->input("phone")),
            birthDate: new Carbon($request->input("birth_date")),
            role: Role::from($request->input("role")),
            owner: $this->getAuthUser(),
            account: $account
        );

        $this->service->notify($saveRelationshipInviteDto);

        return $this->empty();
    }

    /**
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function index(string $accountNumber): Response
    {
        $jointOwners = $this->service->getRelationshipInvites($this->getAuthUser(), $accountNumber);

        return response()->json(new RelationshipInviteCollection($jointOwners));
    }

    /**
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function delete(string $accountNumber, string $jointOwner): Response
    {
        $this->service->deleteRelationshipInvite($this->getAuthUser(), $accountNumber, $jointOwner);

        return $this->empty();
    }

    private function formatPhoneNumber($phoneNumber): string
    {
        $phoneNumber = preg_replace("/[^0-9]/", "", $phoneNumber);

        if ($phoneNumber[0] === "8" || strlen($phoneNumber) === 10) {
            $phoneNumber = "7" . substr($phoneNumber, -10);
        }

        return "+" . $phoneNumber;
    }
}
