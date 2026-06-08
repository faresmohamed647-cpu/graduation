<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => ['بيانات الدخول غير صحيحة.'],
            ]);
        }

        $user = $request->user();
        $selectedRole = strtolower((string) $request->input('role', ''));

        if ($selectedRole !== '' && $selectedRole !== strtolower((string) $user->role)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => ['This account is registered as ' . $user->role . '. Please choose the correct login tab.'],
            ]);
        }

        // Check if driver account is pending approval
        if ($user->role === 'driver') {
            $driver = $user->driverProfile;
            if ($driver && in_array($driver->status, ['pending', 'interview_scheduled'])) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $msg = 'حسابك لسه تحت المراجعة.';
                if ($driver->interview_date) {
                    $msg .= ' ميعاد المقابلة: ' . $driver->interview_date->format('Y-m-d h:i A');
                }

                throw ValidationException::withMessages([
                    'email' => [$msg],
                ]);
            }

            if ($driver && $driver->status === 'rejected') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                throw ValidationException::withMessages([
                    'email' => ['تم رفض طلبك. تواصل مع الإدارة لمزيد من المعلومات.'],
                ]);
            }
        }

        $request->session()->regenerate();

        // Issue a Sanctum token so the dashboard JS can make authenticated API calls.
        // Revoke any previous dashboard-session token first to keep things tidy.
        $user->tokens()->where('name', 'dashboard-session')->delete();
        $apiToken = $user->createToken('dashboard-session')->plainTextToken;
        $request->session()->put('api_token', $apiToken);

        // If this is an AJAX request return JSON with the token.
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'   => true,
                'api_token' => $apiToken,
                'redirect'  => $this->redirectPathForRole($user->role),
            ]);
        }

        return $this->redirectUser($user);
    }

    /**
     * Determine the redirect path for a given role.
     */
    protected function redirectPathForRole(string $role): string
    {
        return match ($role) {
            'admin'        => '/admin',
            'school_admin' => '/school-admin',
            'driver'       => '/driver',
            'parent'       => '/parent',
            default        => '/',
        };
    }

    /**
     * Redirect user to their respective dashboard based on role.
     */
    protected function redirectUser($user)
    {
        return redirect()->to($this->redirectPathForRole($user->role));
    }

    public function logout(Request $request)
    {
        // Revoke the dashboard Sanctum token so it can't be reused.
        $request->user()?->tokens()->where('name', 'dashboard-session')->delete();
        $request->session()->forget('api_token');

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to('/');
    }
}
