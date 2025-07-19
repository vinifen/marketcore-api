<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use App\Exceptions\AuthException;

class EnsureUserIsSelf
{
    public function handle(Request $request, Closure $next): Response
    {
        $routeUser = $request->route('user');
        $authUser = $request->user();

        if ($routeUser instanceof User) {
            if ($routeUser->id !== $authUser->id) {
                throw new AuthException(
                    ["auth" => ['You can only access your own data.']],
                    null,
                    403
                );
            }
        } elseif (is_numeric($routeUser)) {
            if ((int) $routeUser !== $authUser->id) {
                throw new AuthException(
                    ["auth" => ['You can only access your own data.']],
                    null,
                    403
                );
            }
        }

        return $next($request);
    }
}
