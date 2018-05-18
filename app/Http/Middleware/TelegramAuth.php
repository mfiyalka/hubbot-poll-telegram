<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class TelegramAuth
{
    public function handle($request, Closure $next)
    {
        if(Auth::check())
        {
            return $next($request);
        }

        return redirect()->back();
    }
}
