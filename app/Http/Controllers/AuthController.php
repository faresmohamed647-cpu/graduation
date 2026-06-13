<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\SchoolLoginProvisioner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request, SchoolLoginProvisioner $schoolLoginProvisioner)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = User::where('email', $credentials['email'])->first();

            if (! $user) {
                $user = $schoolLoginProvisioner->provisionFromExistingApplication(
                    $credentials['email'],
                    $credentials['password']
                );
            } elseif ($schoolLoginProvisioner->repairPasswordFromPlainText($user, $credentials['password'])) {
                $user->refresh();
            }

            if ($user) {
                Auth::login($user, $request->boolean('remember'));
            }
        }

        if (! Auth::check()) {
            throw ValidationException::withMessages([
                'email' => ['Invalid login details.'],
            ]);
        }

        $user = $request->user();
        $selectedRole = strtolower((string) $request->input('role', ''));
        $userRole = strtolower((string) $user->role);
        $targetRole = $selectedRole === 'school' ? 'school_admin' : $selectedRole;

        if ($targetRole !== '' && $targetRole !== $userRole) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => ['This account is registered as ' . ($user->role ?? 'user') . '. Please choose the correct login tab.'],
            ]);
        }

        // Keep driver approval behavior unchanged. Schools are allowed into their
        // locked dashboard so they can complete the required profile form.
        if ($userRole === 'driver') {
            $driver = $user->driverProfile;
            if ($driver && in_array($driver->status, ['pending', 'interview_scheduled'], true)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $msg = 'Your account is still under review.';
                if ($driver->interview_date) {
                    $msg .= ' Interview date: ' . $driver->interview_date->format('Y-m-d h:i A');
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
                    'email' => ['Your request was rejected. Please contact administration for more information.'],
                ]);
            }
        }

        $request->session()->regenerate();

        // Issue a Sanctum token so the dashboard JS can make authenticated API calls.
        // Revoke any previous dashboard-session token first to keep things tidy.
        $user->tokens()->where('name', 'dashboard-session')->delete();
        $apiToken = $user->createToken('dashboard-session')->plainTextToken;
        $request->session()->put('api_token', $apiToken);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'api_token' => $apiToken,
                'redirect' => $this->redirectPathForRole($user->role),
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
            'admin' => '/admin',
            'school_admin' => '/school-admin',
            'driver' => '/driver',
            'parent' => '/parent',
            default => '/',
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
