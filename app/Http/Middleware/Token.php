<?php

namespace App\Http\Middleware;

use App\Services\Response;
use Closure;
use Illuminate\Support\Facades\Auth;

class Token
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->token;

        $token = str_replace('\/', '/', $token);

        $user = \App\User::where('api_token', $token)->first();

        if ($user) {
            Auth::login($user);
            return $next($request);
        }

        return Response::message('unauthorized user', 401);
    }
}
