<?php

namespace App\Console\Commands;

use App\Models\Admin\Admin;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Class InitRoles
 *
 * @package App\Console\Commands
 */
class InitRoles extends Command
{
    private const GUARD = 'admin';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:init';

    public function handle()
    {
        Role::create(['name' => Admin::ROLE_ADMIN, 'guard_name' => self::GUARD]);

        /** @var Role $roleMarketing */
        $roleMarketing = Role::create(['name' => Admin::ROLE_MARKETING, 'guard_name' => self::GUARD]);
        $roleMarketing->givePermissionTo(
            Permission::create(['name' => 'instructions', 'guard_name' => self::GUARD]),
            Permission::create(['name' => 'notifications', 'guard_name' => self::GUARD]),
            Permission::create(['name' => 'projects', 'guard_name' => self::GUARD]),
            Permission::create(['name' => 'bank-info', 'guard_name' => self::GUARD]),
        );

        /** @var Role $roleUk */
        $roleUk = Role::create(['name' => Admin::ROLE_UK, 'guard_name' => self::GUARD]);
        $roleUk->givePermissionTo(
            Permission::create(['name' => 'news', 'guard_name' => self::GUARD]),
            Permission::create(['name' => 'banners', 'guard_name' => self::GUARD]),
            Permission::create(['name' => 'ads', 'guard_name' => self::GUARD]),
            Permission::create(['name' => 'uk-projects', 'guard_name' => self::GUARD]),
        );

        $this->info('Roles were created.');
    }
}
