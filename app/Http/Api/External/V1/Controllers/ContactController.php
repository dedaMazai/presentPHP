<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Http\Resources\Contact\ContactGroupResource;
use App\Models\Settings;
use App\Services\Account\AccountRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ContactController
 *
 * @package App\Http\Api\External\V1\Controllers
 */
class ContactController extends Controller
{
    public function __construct(private AccountRepository $repository)
    {
    }

    /**
     * @throws ValidationException
     * @throws InvalidArgumentException
     */
    public function index(string $accountNumber): Response
    {
        $account = $this->repository->getAccountByNumber($accountNumber);
        if (!$account->getUkProject()) {
            throw new NotFoundHttpException('UkProject not found.');
        }

        return response()->json(new ContactGroupResource([
            'project' => $account->getUkProject()->contacts()->get(),
            'common' => $this->findSettings()->contacts()->get(),
        ]));
    }

    private function findSettings(): Settings
    {
        /* @var Settings $settings */
        $settings = Settings::first();
        if ($settings === null) {
            throw new NotFoundHttpException('Settings not found.');
        }

        return $settings;
    }
}
