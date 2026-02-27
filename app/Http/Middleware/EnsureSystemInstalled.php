<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSystemInstalled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userCount = \App\Models\User::count();

        if ($userCount === 0) {
            if (!$request->routeIs('setup') && !$request->routeIs('setup.store') && !$request->routeIs('setup.2fa.verify') && !$request->routeIs('lang.switch') && !$request->is('livewire/*')) {
                return redirect()->route('setup');
            }
        } else {
            if ($request->routeIs('setup') || $request->routeIs('setup.2fa.verify')) {
                if (!$request->session()->has('setup_user_id')) {
                    return redirect('/');
                }
            }
        }

        return $next($request);
    }
}
