<?php

namespace App\Http\Middleware;

use Closure;

class LowerUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(preg_match('/[A-Z]/', $request->fullUrl())){
            return redirect(strtolower($request->fullUrl()));            
        }
        return $next($request);
    }
}
