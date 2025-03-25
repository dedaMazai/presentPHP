<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Auth\VerificationCode\VerificationCase;
use App\Auth\VerificationCode\VerificationCodeManager;
use App\Http\Api\External\V1\Requests\DeleteUserRequest;
use App\Http\Api\External\V1\Traits\VerifyCode;
use App\Http\Resources\DeletingReasonCollection;
use App\Http\Resources\DeletingReasonResource;
use App\Http\Resources\UserInfoResource;
use App\Http\Resources\UserResource;
use App\Models\User\DeletingReason;
use App\Models\User\NotificationChannel;
use App\Services\Account\AccountRepository;
use App\Services\Contract\ContractRepository;
use App\Services\Contract\ContractService;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\Sales\Customer\CustomerRepository;
use App\Services\User\UserService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class UserController
 *
 * @package App\Http\Api\External\V1\Controllers
 */
class UserController extends Controller
{
    use VerifyCode;

    public function __construct(
        private VerificationCodeManager $verificationCodeManager,
        private UserService $userService,
        private AccountRepository $repository,
        private CustomerRepository $customerRepository
    ) {
    }

    /**
     * @return Response
     * @throws AuthenticationException
     */
    public function view(
        ContractRepository $contractRepository,
        UserService $userService
    ): Response {
        $user = $this->getAuthUser();

        $accountsNumbers = $contractRepository->getContractsForUser($user->crm_id);
        $userService->syncAccountForUser($this->getAuthUser(), $accountsNumbers);

        return response()->json(new UserResource($this->getAuthUser()));
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws ValidationException
     * @throws AuthenticationException
     * @throws Throwable
     */
    public function update(Request $request): Response
    {
        $this->validate($request, [
            'email' => 'required|email|max:255',
        ]);

        $this->userService->updateUserEmail($this->getAuthUser(), $request->input('email'));

        $user = $this->getAuthUser();
        $user->email = $request->input('email');
        $user->saveOrFail();

        return response()->json(new UserResource($user));
    }

    /**
     * @throws AuthenticationException
     */
    public function toggleNotification(string $notificationType): Response
    {
        $user = $this->getAuthUser();
        $user->toggleNotification(NotificationChannel::from($notificationType));

        return $this->empty();
    }

    /**
     * @throws AuthenticationException
     * @throws ValidationException
     * @throws Throwable
     */
    public function updatePushToken(Request $request): Response
    {
        $this->validate($request, [
            'token' => 'required|string|max:255',
        ]);

        $user = $this->getAuthUser();

        $user->push_token = $request->input('token');
        $user->saveOrFail();

        return $this->empty();
    }

    public function getDeletingReasons(): Response
    {
        return response()->json(new DeletingReasonCollection(DeletingReason::all()));
    }

    /**
     * @param DeleteUserRequest $request
     * @param UserService       $service
     *
     * @return Response
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws AuthenticationException
     * @throws ValidationException
     */
    public function delete(DeleteUserRequest $request, UserService $service): Response
    {
        $user = $this->getAuthUser();
        $this->verifyCode(
            case: VerificationCase::deleteAccount(),
            phone: $user->phone,
            code: $request->input('code'),
        );
        /** @var DeletingReason|string $reason */
        $reason = DeletingReason::firstWhere('value', $request->input('reason')) ?? $request->input('reason');

        $service->delete($user, $reason);
        $user->tokens()->delete();

        return $this->empty();
    }

    public function fullInfo()
    {
        $user = $this->getAuthUser();
        $userInfo = $this->customerRepository->getById($user->crm_id);

        return response()->json(new UserInfoResource($userInfo));
    }
}
