<?php

namespace App\Models\User;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BanHistory extends Model
{
    use HasFactory;

    protected $guarded = false;

    protected $table = 'ban_history';

    public $timestamps = false;

    public function scopeByActive($query)
    {
        return $query->where('timestamp', '<', Carbon::now()->addMinute()->toDateTimeString());
    }

    public function scopeByIp($query, $ip)
    {
        return $query->where('ip_address', $ip);
    }

    public function scopeByPhone($query, $number)
    {
        return $query->where('phone_number', $number);
    }

    public function scopeByBanned($query)
    {
        return $query->where('failed_attempts', '>', 2);
    }

    public function incrementAttempts($query)
    {
        return $query->increment('failed_attempts');
    }
}
