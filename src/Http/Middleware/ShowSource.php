<?php

namespace Dcat\Admin\Http\Middleware;

use Closure;
use Dcat\Admin\Admin;
use Illuminate\Http\Request;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Response;

class ShowSource
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($class = $request->input('show_source')) {
            $user = Admin::user();
            if ($user && $user->isAdministrator()) {
                $reflection = new ReflectionClass(is_numeric($class) ? $request->route()->getControllerClass() : $class);
                $filePath = $reflection->getFileName();
                show_source($filePath);
                exit;
            }
        }
        return $next($request);
    }
}
