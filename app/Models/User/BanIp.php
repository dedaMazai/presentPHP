<?php

namespace App\Models\User;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BanIp extends Model
{
    use HasFactory;

    protected $guarded = false;

    protected $table = 'ban_ip';

    public $timestamps = false;

    public function scopeByActive($query)
    {
        return $query->where('unlock_time', '>', Carbon::now()->toDateTimeString());
    }

    public function scopeByIp($query, $ip)
    {
        return $query->where('ip_address', $ip);
    }
}
