<?php

namespace App\Models\User;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BanPhone extends Model
{
    use HasFactory;

    protected $guarded = false;

    protected $table = 'ban_phone';

    public $timestamps = false;

    public function scopeByActive($query)
    {
        return $query->where('unlock_time', '>', Carbon::now()->toDateTimeString());
    }

    public function scopeByIp($query, $ip_address)
    {
        return $query->where('ip_address', $ip_address);
    }

    public function scopeByPhone($query, $phone_number)
    {
        return $query->where('phone_number', $phone_number);
    }
}
