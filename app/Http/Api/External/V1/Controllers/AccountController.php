<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Http\Resources\AccountAdditionalInfoResource;
use App\Http\Resources\AccountCollection;
use App\Http\Resources\AccountResource;
use App\Http\Resources\Article\DetailArticleResource;
use App\Models\Account\Account;
use App\Models\Article\Article;
use App\Models\UkProject;
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
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Psr\SimpleCache\InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class AccountController
 *
 * @package App\Http\Api\External\V1\Controllers
 */
class AccountController extends Controller
{
    public function __construct(private AccountRepository $repository)
    {
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
    public function index(
        AccountService $accountService,
        ContractRepository $contractRepository,
        ContractService $contractService,
        UserService $userService
    ): Response {
        $user = $this->getAuthUser();

        $contracts = $contractRepository->getContracts($user->crm_id);
        $contractService->updateRelationsByContracts($contracts, $user);

        $accountsNumbers = $contractService->getAccountsFromContracts($contracts);
        $accounts = [];

        if ($accountsNumbers != null && $accountsNumbers != []) {
            $accounts = $this->repository->getAccountsByNumbers($accountsNumbers)['accounts'];
        }

        if ($accounts != null && $accounts != []) {
            $accountNumbers = [];
            $accounts = array_filter($accounts, function ($element) {
                if (!empty($element)) {
                    return true;
                } else {
                    return false;
                }
            });

            foreach ($accounts as $account) {
                $accountNumbers[] = $account->getNumber();
            }

            $contracts = array_filter($contracts, function ($element) use ($accountNumbers) {
                return in_array($element->getPersonalAccount(), $accountNumbers);
            });
            $accounts = $contractRepository->getDetailContracts($contracts, $accounts);

            foreach ($accounts as $account) {
                $account->role = "5";
            }

            $userService->syncAccount($user, $accounts);
            $userService->subscribeToPushTopicsV1($user, $accounts);

            $accountService->updateAccounts($user, $accounts, $contracts);
        }

        return response()->json(new AccountCollection($accounts));
    }

    /**
     * @throws InvalidArgumentException
     * @throws ValidationException
     * @throws AuthenticationException
     */
    public function show(string $accountNumber): Response
    {
        $account = $this->repository->getAccountByNumber($accountNumber);

        return response()->json(new AccountResource($account));
    }

    /**
     * @throws InvalidArgumentException
     * @throws ValidationException
     */
    public function showAdditionalInfo(string $accountNumber): Response
    {
        $account = $this->repository->getAccountByNumber($accountNumber);
        if (!$account->getUkProject()) {
            throw new RuntimeException('UkProject not found.');
        }

        return response()->json(new AccountAdditionalInfoResource($account));
    }

    public function showAdditionalInfoArticle(string $accountNumber, int $articleId): Response
    {
        $account = $this->repository->getAccountByNumber($accountNumber);
        if (!$account->getUkProject()) {
            throw new RuntimeException('UkProject not found.');
        }

        return response()->json(new DetailArticleResource($this->findArticle($account->getUkProject(), $articleId)));
    }

    private function findArticle(UkProject $project, int $id): Article
    {
        /* @var Article $article */
        $article = $project->articles()->find($id);
        if ($article === null) {
            throw new NotFoundHttpException('Article not found.');
        }

        return $article;
    }
}
