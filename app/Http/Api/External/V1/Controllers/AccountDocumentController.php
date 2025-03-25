<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Http\Resources\AccountDocumentCollection;
use App\Services\Account\AccountDocumentRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AccountDocumentController
 *
 * @package App\Http\Api\External\V1\Controllers
 */
class AccountDocumentController extends Controller
{
    public function __construct(private AccountDocumentRepository $repository)
    {
    }

    /**
     * @throws InvalidArgumentException
     * @throws ValidationException
     */
    public function index(string $accountNumber): Response
    {
        $accountDocuments = $this->repository->getAllByAccountNumber($accountNumber);

        return response()->json(new AccountDocumentCollection($accountDocuments));
    }
}
