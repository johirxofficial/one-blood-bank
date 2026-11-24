<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless(
            $user?->isStaff() && $user->is_active && $user->banned_at === null,
            403,
            'এই এলাকায় প্রবেশের অনুমতি নেই।',
        );

        return $next($request);
    }
}
