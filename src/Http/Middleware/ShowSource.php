<?php

namespace Dcat\Admin\Http\Middleware;

use Closure;
use Dcat\Admin\Admin;
use Illuminate\Http\Request;
use ReflectionClass;
use ReflectionMethod;
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
                $class = is_numeric($class) ? $request->route()->getControllerClass() : $class;
                if ($request->input('methods')) {
                    $reflectionClass = new ReflectionClass(\app($class));
                    $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
                    $list = array_map(function($item) {
                        $paramNames = [];
                        foreach($item->getParameters() ?: [] as $p) {
                            $paramNames[] = '$' . $p->getName();
                        }
                        return sprintf("%s(%s)", $item->getName(), implode(', ', $paramNames));
                    }, $methods);
                    sort($list);
                    dd($list);
                } else {
                    $reflection = new ReflectionClass($class);
                    $filePath = $reflection->getFileName();
                    show_source($filePath);
                }
                exit;
            }
        }
        return $next($request);
    }
}
