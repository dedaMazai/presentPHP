<?php

namespace App\Models\User;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ban extends Model
{
    use HasFactory;

    protected $guarded = false;

    public function scopeByActive($query)
    {
        return $query->where('unlock_time', '<', Carbon::now()->getTimestamp());
    }

    public function scopeByIp($query, $ip)
    {
        return $query->where('ip', $ip);
    }
}
