<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->canManageSettings(), 403, 'শুধুমাত্র অ্যাডমিন এই অংশ ব্যবহার করতে পারবেন।');

        return $next($request);
    }
}
