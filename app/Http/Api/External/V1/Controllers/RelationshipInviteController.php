<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Http\Api\External\V1\Requests\SaveRelationshipInviteRequest;
use App\Http\Resources\DescriptionOfRolesCollection;
use App\Http\Resources\RelationshipInviteCollection;
use App\Models\Relationship\RelationshipInvite;
use App\Models\Role;
use App\Models\V2\DescriptionOfRoles;
use App\Notifications\SendRelationshipSms;
use App\Services\Account\AccountRepository;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\RelationshipInvite\Dto\SaveRelationshipInviteDto;
use App\Services\RelationshipInvite\Exceptions\UnableToFindContractException;
use App\Services\RelationshipInvite\Exceptions\UnableToSetJointOwnerException;
use App\Services\RelationshipInvite\RelationshipInviteService;
use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RelationshipInviteController
 *
 * @package App\Http\Api\External\V1\Controllers
 */
class RelationshipInviteController extends Controller
{
    public function __construct(private AccountRepository $repository)
    {
    }

    /**
     * @throws AuthenticationException
     */
    public function index(string $accountNumber): Response
    {
        /** @var RelationshipInvite[] $relationshipInvites */
        $relationshipInvites = RelationshipInvite::byAccountNumber($accountNumber)->get();

        return response()->json(new RelationshipInviteCollection($relationshipInvites));
    }

    /**
     * @throws UnableToFindContractException
     * @throws BadRequestException
     * @throws UnableToSetJointOwnerException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function store(
        string $accountNumber,
        SaveRelationshipInviteRequest $request,
        RelationshipInviteService $service,
    ): Response {
        Gate::authorize('manipulate-relationship', $accountNumber);

        if ($this->getAuthUser()->phone == $request->input('phone')) {
            throw ValidationException::withMessages([
                'phone' => ['You cannot invite yourself.'],
            ])->status(409);
        }

        $relationshipInviteExist = RelationshipInvite::byAccountNumber($accountNumber)
            ->byPhone($request->input('phone'))
            ->first();
        if ($relationshipInviteExist) {
            throw ValidationException::withMessages([
                'phone' => ['Client with this phone number already invited.'],
            ])->status(409);
        }

        $account = $this->repository->getAccountByNumber($accountNumber);

        $saveRelationshipInviteDto = new SaveRelationshipInviteDto(
            firstName: $request->input('first_name'),
            lastName: $request->input('last_name'),
            phone: $this->formatPhoneNumber($request->input('phone')),
            birthDate: new Carbon($request->input('birth_date')),
            role: Role::tenant(),
            owner: $this->getAuthUser(),
            account: $account
        );
        $service->store($accountNumber, $saveRelationshipInviteDto);

        $this->notify($saveRelationshipInviteDto);

        return $this->empty();
    }

    private function notify(SaveRelationshipInviteDto $inviteDto): void
    {
        $notifiable = new AnonymousNotifiable();
        $notifiable->route('sms', $inviteDto->phone);
        $notifiable->notify(new SendRelationshipSms($inviteDto));
    }

    /**
     * @throws UnableToFindContractException
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function destroy(string $accountNumber, int $id, RelationshipInviteService $service): Response
    {
        Gate::authorize('manipulate-relationship', $accountNumber);

        $service->delete($accountNumber, $id);

        return $this->empty();
    }

    public function description(): Response
    {
        return response()->json(new DescriptionOfRolesCollection(DescriptionOfRoles::all()));
    }

    private function formatPhoneNumber($phoneNumber): string
    {
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        if (str_starts_with($phoneNumber, '8') || strlen($phoneNumber) === 10) {
            $phoneNumber = '7' . substr($phoneNumber, -10);
        }

        return '+' . $phoneNumber;
    }
}
