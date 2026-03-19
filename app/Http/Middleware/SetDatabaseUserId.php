<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SetDatabaseUserId
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            DB::statement('SET @current_user_id = ?', [auth()->id()]);
        }

        return $next($request);
    }
}
