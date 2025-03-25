<?php

namespace App\Models;

use App\Components\Publication\Publicable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UnauthorizedPushtokens
 *
 * @property int            $id
 * @property string         $push_token
 * @property Carbon|null    $created_at
 * @property Carbon|null    $updated_at
 *
 * @package App\Models
 */
class UnauthorizedPushtokens extends Model
{
    use HasFactory;
    use Publicable;

    /** @inheritdoc */
    protected $fillable = [
        'push_token',
    ];
}
