<?php

namespace App\Http\Middleware;

use App\Models\User\BanIp;
use App\Models\User\BanPhone;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class AuthAttempts
{
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();
        $phone = $request->get('phone');

        $ban = BanIp::byIp($ip)->byActive()->get();

        if (!$ban) {
            $ban = BanPhone::byPhone($phone)->byActive()->get();
        }

        if ($ban->count() !== 0) {
            header("X-RateLimit-Reset:". $ban->first()['unlock_time']);
            header("X-RateLimit-Attempt:". $ban->first()['attempts']);
            header("retry-after:". Carbon::parse($ban->first()['unlock_time'])->timestamp-Carbon::now()->timestamp);
            return response()->json(['error' => 'Too many attempts.'], 429);
        }
        return $next($request);
    }
}
