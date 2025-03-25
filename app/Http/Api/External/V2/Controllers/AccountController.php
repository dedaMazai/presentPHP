<?php

namespace App\Http\Api\External\V2\Controllers;

use App\Http\Resources\AccountCollection;
use App\Services\Account\AccountRepository;
use App\Services\Account\AccountService;
use App\Services\Contract\ContractRepository;
use App\Services\Contract\ContractService;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\RelationshipInvite\Exceptions\UnableToFindContractException;
use App\Services\RelationshipInvite\Exceptions\UnableToSetJointOwnerException;
use App\Services\User\UserService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AccountController
 *
 * @package App\Http\Api\External\V1\Controllers
 */
class AccountController extends Controller
{
    public function __construct(
        private readonly AccountRepository $repository
    ) {
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws UnableToFindContractException
     * @throws UnableToSetJointOwnerException
     * @throws AuthenticationException
     * @throws ValidationException
     * @throws InvalidArgumentException
     */
    public function index(UserService $userService): Response
    {
        $user = $this->getAuthUser();
        $accounts = $this->repository->getAccountsByNumbersFromDb($user->id);

        if (!empty($accounts)) {
            $userService->subscribeToPushTopicsV2($user, $accounts['accounts']);

            $accounts['accounts'] = array_map(function ($account) {
                unset($account['classifier_uk_id']);
                unset($account['build_zid']);
                unset($account['uk_project_id']);
                return $account;
            }, $accounts['accounts']);

            $accounts['accounts'][] = ['accounts_groups' => $accounts['accountsGroup']];

            return response()->json($accounts['accounts']);
        }

        return response()->json([]);
    }
}
