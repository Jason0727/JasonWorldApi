<?php

namespace App\Http\Middleware;

use Closure;
use Dingo\Api\Facade\API;
use Illuminate\Contracts\Auth\Factory as Auth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($this->auth->guard($guard)->guest()) {
            return response('Unauthorized.', 401);
        }
        $user = $this->auth->guard($guard)->user();

        if("0000" == $user->role_id)
            return $next($request);

        foreach ($user->role->permissions as $permission)
        {
            if($permission->is_action && $permission->res_action == API::router()->currentRouteAction())
                return $next($request);
        }

        return response('Action Denied.', 401);
    }
}
