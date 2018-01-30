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
        if(preg_match('/[A-Z]/', $request->url())){
            $url = strtolower($request->url());
            if($request->getQueryString()){
                $url.='?'.$request->getQueryString();
            }
            return redirect($url);            
        }
        return $next($request);
    }
}
