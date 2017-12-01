<?php

namespace App\Http\Middleware;

use Closure;
use Dingo\Api\Facade\API;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class ActionFilterMiddleware
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
//        throw new AccessDeniedException(API::router()->currentRouteAction());
        return $next($request);
    }
}
