<?php

namespace App\Http\Middleware;

use Closure;

class BackendAuth
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
        if(!$request->session()->get("userToken")){
            return redirect(route("admin.auth.login"));
        }
        return $next($request);
    }
}
