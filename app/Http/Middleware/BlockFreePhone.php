<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BlockFreePhone
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle($request, Closure $next)
    {
        $requestPhone = $request->input('phone');
        $freePrefixes = [
            '800', '801', '802', '803',
            '804', '805', '806', '807',
            '809', '742', '743', '744',
            '745', '746', '747', '749'
        ];
        $requestPhone = str_replace("+", "", $requestPhone);
        $requestPhone = substr($requestPhone, 0);

        foreach ($freePrefixes as $prefix) {
            if (str_starts_with($requestPhone, $prefix)) {
                return response()->json(['error' => 'Free phone numbers are not allowed.'], 403);
            }
        }

        return $next($request);
    }
}
