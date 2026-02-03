<?php

namespace App\Http\Middleware;

use Closure;
use Session;

class CheckStoreSession
{

    public function handle($request, Closure $next)
    {
        if (!$request->session()->has('STOREUSERNAME')) {
            // user value cannot be found in session
            return redirect('/store');
        }
        return $next($request);

    }

}

?>
