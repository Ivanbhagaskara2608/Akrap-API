<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // check if authenticated user
        if(Auth::check()) {
            // check if user job is secretary, then can request
            if(Auth::user()->role == 'admin') {
                if(Auth::user()->status == 'active') {
                    return $next($request);
                } else {
                    return response()->json([
                        "message" => "Access Denied as your account is inactive"
                    ]); 
                }
            } else {
                return response()->json([
                    "message" => "Access Denied as you're not Admin"
                ]);        
            }

        } else {
            return response()->json([
                "message" => "Access Denied as you're not Admin"
            ]);
        }

        return $next($request);
    }
}
