<?php

namespace App\Http\Admin\Controllers\Account;

use App\Http\Admin\Controllers\Controller;
use App\Models\Account\AccountInfo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Inertia\Response;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class AccountController
 *
 * @package App\Http\Admin\Controllers
 */
class AccountController extends Controller
{
    public function index(Request $request): Response
    {
        $accounts = QueryBuilder::for(AccountInfo::class)
            ->allowedFilters([
                'account_number',
            ])
            ->paginate();

        $accounts->map(function ($account) {
            $users_id = [];
            foreach ($account->accountNumbers()->get() as $accountNumber) {
                $users_id[] = $accountNumber->user_id;
            }

            $account->users_id = implode(",\n", $users_id);

            return $account;
        });

        return inertia('Account/List', [
            'accounts' => $accounts,
            'defaultSorter' => $request->input('sort'),
            'defaultFilters' => $request->input('filter'),
        ]);
    }

    public function update(): RedirectResponse
    {
        Artisan::call('accounts:update');
        Artisan::call('account-numbers:update');

        return redirect()->route('accounts');
    }
}
