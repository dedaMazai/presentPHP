<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Http\Resources\AccountDocumentCollection;
use App\Http\Resources\AccountThemeCollection;
use App\Services\Account\AccountDocumentRepository;
use App\Services\Account\AccountThemeRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AccountDocumentController
 *
 * @package App\Http\Api\External\V1\Controllers
 */
class AccountThemesController extends Controller
{
    public function __construct(private AccountThemeRepository $repository)
    {
    }

    /**
     * @throws InvalidArgumentException
     * @throws ValidationException
     */
    public function index(string $accountNumber): Response
    {
        $accountThemes = $this->repository->getAllByAccountNumber($accountNumber);

        return response()->json(new AccountThemeCollection($accountThemes));
    }
}
