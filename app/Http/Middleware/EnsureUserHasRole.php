<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        abort_unless($user instanceof User && $user->role instanceof UserRole, 403);

        $allowed = array_map(
            fn (string $role): UserRole => UserRole::from($role),
            $roles,
        );

        abort_unless(in_array($user->role, $allowed, true), 403);

        return $next($request);
    }
}
