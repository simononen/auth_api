<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json([
                    'success' => false,
                    'status' => 'Token is Invalid',
                    'error' => true
                ]);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                $token = JWTAuth::parseToken()->refresh();
                return response()->json([
                    'success' => false,
                    'status' => 'Token is Expired',
                    'error' => true,
                    'token' => $token
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'status' => 'Authorization Token not found',
                    'error' => true
                ]);
            }
        }
        return $next($request);
    }
}
