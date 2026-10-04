<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserCanAccessPlatform
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $slug = (string) $request->route('platform');

        abort_unless($user instanceof User && $user->canAccessWorkspace($slug), 404);

        return $next($request);
    }
}
