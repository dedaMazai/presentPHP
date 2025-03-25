<?php

namespace App\Http\Api\External\V2\Controllers;

use App\Auth\VerificationCode\VerificationCodeManager;
use App\Http\Api\External\V1\Traits\VerifyCode;
use App\Http\Resources\UserResource;
use App\Services\Account\AccountRepository;
use App\Services\Contract\ContractRepository;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\User\UserService;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 *
 * @package App\Http\Api\External\V1\Controllers
 */
class UserController extends Controller
{
    use VerifyCode;

    public function __construct(
        private readonly VerificationCodeManager $verificationCodeManager,
        private readonly UserService             $userService,
        private readonly AccountRepository       $repository
    ) {
    }

    /**
     * @return Response
     * @throws AuthenticationException
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function view(
        ContractRepository $contractRepository,
        UserService $userService
    ): Response {
        $user = $this->getAuthUser();

        $accountsNumbers = $contractRepository->getContractsForUser($user->crm_id);
//        $userService->syncAccountForUser($this->getAuthUser(), $accountsNumbers);

        return response()->json(new UserResource($this->getAuthUser()));
    }
}
