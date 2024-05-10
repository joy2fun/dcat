<?php

namespace Dcat\Admin\Http\Middleware;

use Closure;
use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        if ($guards) {
            // set Bearer token from query string if needed
            if ($request->get('token') && ! $request->hasHeader('Authorization')) {
                $request->headers->set('Authorization', 'Bearer ' . $request->get('token'));
            }
            // always response json when exception occurred
            $request->headers->set('Accept', 'application/json', true);
            $this->authenticate($request, $guards);
            Admin::guard()->setUser($request->user());
            return $next($request);
        }

        if (
            !config('admin.auth.enable', true)
            || !Admin::guard()->guest()
            || $this->shouldPassThrough($request)
        ) {
            // better for telescope
            Auth::setUser(Admin::user());
            return $next($request);
        }

        return admin_redirect('auth/login', 401);
    }

    /**
     * Determine if the request has a URI that should pass through verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function shouldPassThrough($request)
    {
        $excepts = array_merge(
            (array) config('admin.auth.except', []),
            Admin::context()->getArray('auth.except')
        );

        foreach ($excepts as $except) {
            if ($request->routeIs($except) || $request->routeIs(admin_route_name($except))) {
                return true;
            }

            $except = admin_base_path($except);

            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if (Helper::matchRequestPath($except)) {
                return true;
            }
        }

        return false;
    }
}
