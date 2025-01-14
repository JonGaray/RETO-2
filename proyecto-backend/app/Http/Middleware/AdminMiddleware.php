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
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()){
            if (Auth::user()->role == 'admin'){
                return $next($request);
            }else{
                return response()->json(['message' => "Forbidden: You do not have the required permission"], 403);
            }
        } else {
            return response()->json(['message' => "Unauthorized: You have not been authenticated"], 401);
        }
    }
}
