<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AbortLocalhost
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->ip() =='::1' || $request->ip() == '127.0.0.1') {
            return response()->json(['Warning' => 'Calls from localhost are not counted'], Response::HTTP_BAD_REQUEST);
        }

        return $next($request);
    }
}
