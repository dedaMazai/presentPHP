<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'jobs';

    public $timestamps = false;

    /** @inheritdoc */
    protected $fillable = [
        'id',
        'attempts',
        'available_at',
        'payload',
        'queue',
        'reserved_at',
        'created_at',
    ];
}
