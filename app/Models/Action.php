<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Class Action
 *
 * @property int    $id
 * @property string $type
 * @property array  $payload
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class Action extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public static function createFromType(string $type, array $payload): self
    {
        $model = new self();
        $model->type = $type;
        $model->payload = $payload;
        $model->saveOrFail();

        return $model;
    }
}
