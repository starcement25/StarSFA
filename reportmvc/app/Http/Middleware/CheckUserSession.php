<?php

namespace App\Http\Middleware;

use Closure;
use Session;

class CheckUserSession
{

    public function handle($request, Closure $next)
    {
        if (!$request->session()->has('DBNAME') && !$request->session()->has('USERNAME')) {
            // user value cannot be found in session
            return redirect('/');
        }
        return $next($request);

    }

}

?>
