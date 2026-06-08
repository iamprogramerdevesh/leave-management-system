<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->to($this->redirectPath(Auth::user()));
        }

        return view('auth.login');
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginField = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'employee_id';

        $user = User::query()->where($loginField, $request->input('login'))->first();

        if(!$user || empty($user)) {
            return response()->json([
                'success' => false,
                'message' => "User doesn't exists.",
            ], 422);
        }

        if (!$user->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'User account is inactive. Please contact super admin.',
            ], 422);
        }

        $credentials = [
            $loginField => $request->input('login'),
            'password' => $request->input('password'),
        ];

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 422);
        }

        $request->session()->regenerate();

        $this->auditService->logLogin($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'redirect' => $this->redirectPath(Auth::user()),
        ]);
    }

    public function logout(Request $request): JsonResponse|RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            $this->auditService->logLogout($user->id);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully.',
                'redirect' => route('login'),
            ]);
        }

        return redirect()->route('login');
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
