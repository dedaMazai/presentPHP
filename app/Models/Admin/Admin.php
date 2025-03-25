<?php

namespace App\Models\Admin;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class Admin
 *
 * @property int    $id
 * @property string $email
 * @property string $password
 * @property string $remember_token
 *
 * @package App\Models\Admin
 */
class Admin extends Model implements AuthenticatableContract
{
    use Authenticatable;
    use HasFactory;
    use HasRoles;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_MARKETING = 'marketing';
    public const ROLE_UK = 'uk';

    public static function register(string $email, string $password, Role $role): Admin
    {
        $model = new self();
        $model->email = $email;
        $model->password = Hash::make($password);
        $model->setRememberToken($token = Str::random(60));
        $model->save();

        $model->assignRole($role);

        return $model;
    }
}
