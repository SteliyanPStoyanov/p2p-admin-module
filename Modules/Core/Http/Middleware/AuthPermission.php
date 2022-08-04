<?php

namespace Modules\Core\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use Modules\Core\Exceptions\AccessDeniedApiException;

class AuthPermission
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure $next
     *
     * @return mixed
     *
     * @throws AccessDeniedApiException
     */
    public function handle(Request $request, Closure $next)
    {

        $route = $request->route();
        $routeName = $route->getName();
        $actionName = $route->getActionMethod();
        $excludedMethods = config('permission.exclude_methods');

        if (empty($route->defaults['description'])) {
            return $next($request);
        }

        if (isset($excludedMethods[$actionName])) {
            $routeName = str_replace(
                $actionName,
                $excludedMethods[$actionName],
                $routeName
            );
        }

        if (Auth::user()->hasPermissionTo($routeName)) {
            return $next($request);
        }

        if ($request->wantsJson()) {
            throw new AccessDeniedApiException('Access denied!', 403);
        }

        return back()->with('fail', 'Access denied!');
    }
}
