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

        // If this is an AJAX request (from the unified login JS),
        // return JSON instead of a redirect so jQuery can handle it
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect' => match ($user->role) {
                    'admin'  => '/admin',
                    'driver' => '/driver',
                    'parent' => '/parent',
                    default  => '/',
                },
            ]);
        }

        return $this->redirectUser($user);
    }

    /**
     * Redirect user to their respective dashboard based on role.
     */
    protected function redirectUser($user)
    {
        return match ($user->role) {
            'admin'  => redirect()->to('/admin'),
            'driver' => redirect()->to('/driver'),
            'parent' => redirect()->to('/parent'),
            default  => redirect()->to('/'),
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to('/');
    }
}

