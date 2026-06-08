<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * @param  Closure(Request): Response  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles, true)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to access this resource.',
                ], 403);
            }

            if ($user) {
                return redirect()->to($this->redirectPath($user));
            }

            return redirect()->route('login');
        }

        return $next($request);
    }

    private function redirectPath(User $user): string
    {
        return match ($user->role) {
            User::ROLE_ADMIN => '/admin',
            User::ROLE_MANAGER => '/manager',
            User::ROLE_EMPLOYEE => '/employee',
            default => route('login', absolute: false),
        };
    }
}