<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use Spatie\Permission\Models\Role;


class FirstrunMiddleware
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

        $route = $request->route()->getActionMethod() == "register" || $request->route()->getActionMethod() == "showRegistrationForm";
        if (file_exists(storage_path('installed.json'))) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Forbidden.'], 403);
            }
            return redirect()->route('register');
        }
        return $next($request);
    }
}
