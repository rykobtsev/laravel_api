<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ClearExpiredTokens
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tokenIds = DB::table('personal_access_tokens')
            ->where('last_used_at', '<', now()->subSeconds(config('sanctum.expiration')))
            ->pluck('id');

        DB::table('personal_access_tokens')->whereIn('id', $tokenIds)->delete();

        return $next($request);
    }
}