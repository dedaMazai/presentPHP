<?php

namespace App\Console\Commands;

use App\Models\Admin\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

/**
 * Class CreateAdmin
 *
 * @package App\Console\Commands
 */
class CreateAdmin extends Command
{
    /**
     * @var string
     */
    protected $signature = 'admins:create {email} {password} {role}';

    private const GUARD = 'admin';

    /**
     * @var string
     */
    protected $description = 'Create new admin with role.';

    /**
     * @return int
     */
    public function handle(): int
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        $role = $this->argument('role');

        $validator = Validator::make([
            'email' => $email,
            'password' => $password,
            'role' => $role,
        ], [
            'email' => ['required', 'email', 'unique:admins,email'],
            'password' => ['required', 'min:8'],
            'role' => ['required', 'exists:roles,name'],
        ]);

        if ($validator->fails()) {
            $this->alert('Admin User not created. See error messages below:');

            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return 1;
        }

        $role = Role::findByName($role, self::GUARD);
        Admin::register($email, $password, $role);

        $this->info('Admin account created.');

        return 0;
    }
}
