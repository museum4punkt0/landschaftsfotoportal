<?php

namespace App\Http\Middleware;

use App;
use Config;
use Session;
use Closure;

class Locale
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
        if (!Session::has('locale')) {
            Session::put('locale', Config::get('app.locale'));
        }
        
        App::setLocale(Session::get('locale'));
        
        return $next($request);
    }
}
