<?php

namespace App\Models\V2;

use App\Components\Publication\Publicable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class DescriptionOfRoles
 *
 * @property int            $role_code
 * @property string         $role_name
 * @property string         $role_scope

 *
 * @package App\Models\V2
 */
class DescriptionOfRoles extends Model
{
    /** @inheritdoc */
    protected $fillable = [
        'role_code',
        'role_name',
        'role_scope',
    ];

    public $timestamps = false;
}
