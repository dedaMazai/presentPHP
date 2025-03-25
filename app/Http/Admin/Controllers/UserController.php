<?php

namespace App\Http\Admin\Controllers;

use App\Components\QueryBuilder\Filters\FilterByNames;
use App\Components\QueryBuilder\Filters\FilterByStatus;
use App\Components\QueryBuilder\Filters\FiltersCreatedBetween;
use App\Http\Admin\Requests\CreateUserRequest;
use App\Http\Admin\Requests\SaveUserRequest;
use App\Models\TransactionLog\TransactionExport;
use App\Models\User\BanPhone;
use App\Models\User\User;
use App\Models\User\UserExport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class UserController
 *
 * @package App\Http\Admin\Controllers
 */
class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $users = QueryBuilder::for(User::class)
            ->withTrashed()
            ->allowedFilters([
                AllowedFilter::exact('id'),
                'crm_id',
                AllowedFilter::partial('phone'),
                AllowedFilter::custom('creation_period', new FiltersCreatedBetween()),
                AllowedFilter::custom('ban', new FilterByStatus()),
                AllowedFilter::custom('names', new FilterByNames()),
            ])
            ->allowedSorts(['created_at', 'updated_at'])
            ->with(['ban','relationships.accountInfo'])
            ->paginate();

        $users->map(function ($user) {
            $user->ban_status = 'Активен';

            foreach ($user->ban()->get() as $ban) {
                if (Carbon::parse($ban->unlock_time) > Carbon::now()->toDateTimeString()) {
                    $user->ban_status = 'Заблокирован';
                }
            }

            $accountNumbers = [];
            $accountInfos = [];
            foreach ($user->relationships()->get() as $relationship) {
                $accountNumbers[] = $relationship->account_number;
                $accountInfos[] = $relationship->accountInfo?->project_name;
            }

            $user->account_numbers = implode(",\n", $accountNumbers);
            $user->id_projects_uk = implode(",\n", array_unique($accountInfos));
            $user->status = $user->status ? 'Активен' : 'Не активен';

            return $user;
        });

        return inertia('Users/List', [
            'users' => $users,
            'defaultSorter' => $request->input('sort'),
            'defaultFilters' => $request->input('filter'),
        ]);
    }

    public function export()
    {
        return Excel::download(new UserExport(), 'users.xlsx');
    }


    public function create(Request $request): Response
    {
        return inertia('Users/Create');
    }

    public function store(CreateUserRequest $request): RedirectResponse
    {
        $user = User::create([
            'crm_id' => $request->input('crm_id'),
            'phone' => '+7'.$request->input('phone'),
            'email' => $request->input('email'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'middle_name' => $request->input('middle_name'),
            'birth_date' => $request->input('birth_date'),
            //'manager_control' => $request->input('manager_control'),
            ]);


        if ($request->input('password') != null) {
            $userPassword = User::find($user->id);
            $userPassword->changePassword($request->input('password'));
            $userPassword->save();
        }

        return redirect()->route('users.edit', [
            'id' => $user->id,
        ]);
    }

    public function edit(int $id): Response
    {
        $user = $this->findUser($id);
        $user->ban_status = 'Активен';
        foreach ($user->ban()->get() as $ban) {
            if (Carbon::parse($ban->unlock_time) > Carbon::now()->toDateTimeString()) {
                $user->ban_status = 'Заблокирован';
            }
        }

        $user->unlock_time = $user->ban()->first()?->unlock_time;
        $phone = str_replace("+", "", $user->phone);
        $phone = substr($phone, 1);
        $user->phone = $phone;

        return inertia('Users/Edit', [
            'user' => $user,
        ]);
    }

    public function unlock(int $id): Response
    {
        $user = $this->findUser($id);
        BanPhone::where('phone_number', $user->phone)->delete();

        return inertia('Users/Edit', [
            'user' => $user,
        ]);
    }

    public function update(int $id, SaveUserRequest $request): RedirectResponse
    {
        $user = $this->findUser($id);
        $user->update(
            [
                'crm_id' => $request->crm_id,
                'phone' => '+7' . $request->phone,
                //'manager_control' => $request->manager_control,
                'status' => $request->status,
            ]
        );

        if (!$user->status) {
            $user->tokens()->delete();
        }

        if ($request->input('password') != null && $request->input('mode')) {
            $userPassword = User::find($id);
            $userPassword->changePassword($request->input('password'));
            $userPassword->save();
        }

        return redirect()->route('users.edit', [
            'id' => $user->id,
        ]);
    }

    private function findUser(int $id): User
    {
        return User::withTrashed()->find($id);
    }
}
