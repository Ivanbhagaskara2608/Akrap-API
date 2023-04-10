<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SecretaryMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if(Auth::check()) {
            if(Auth::user()->job == 'secretary') {
                return $next($request);
            } else {
                return response()->json([
                    "message" => "Access Denied as you're not Secretary"
                ]);        
            }

        } else {
            return response()->json([
                "message" => "Access Denied as you're not Secretary"
            ]);
        }

        return $next($request);
    }
}
