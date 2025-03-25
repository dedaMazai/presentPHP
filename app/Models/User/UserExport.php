<?php

namespace App\Models\User;

use Maatwebsite\Excel\Concerns\FromCollection;

class UserExport implements FromCollection
{
    public function collection()
    {
        $users = User::with(['relationships.accountInfo'])->get();

        $users->map(function ($user) {
            $accountNumbers = [];
            $accountInfos = [];
            foreach ($user->relationships()->get() as $relationship) {
                $accountNumbers[] = $relationship->account_number;
                $accountInfos[] = $relationship->accountInfo?->project_name;
            }

            $user->account_numbers = implode(",\n", $accountNumbers);
            $user->id_projects_uk = implode(",\n", $accountInfos);

            return $user;
        });

        return $users;
    }
}
