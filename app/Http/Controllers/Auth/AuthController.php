<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    /**
     * Check if email is available
     */
    public function checkEmail(Request $request)
    {
        $email = strtolower(trim($request->query('email', '')));

        if (empty($email)) {
            return response()->json([
                'available' => false,
                'message' => 'Email is required.',
            ], 400);
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'available' => false,
                'message' => 'Please enter a valid email address.',
            ], 400);
        }

        $exists = User::where('email', $email)->exists();

        return response()->json([
            'available' => ! $exists,
            'message' => $exists ? 'This email address is already registered.' : 'Email is available.',
        ]);
    }

    /**
     * Web user registration handler
     */
    public function webRegister(\App\Http\Requests\RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'full_name' => trim($validated['full_name']),
            'father_name' => trim($validated['father_name']),
            'cnic' => $validated['cnic'],
            'email' => strtolower(trim($validated['email'])),
            'phone' => $validated['phone'],
            'password' => $validated['password'],
            'role' => 'STUDENT',
            'is_verified' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('student.dashboard')->with('success', 'Registration successful! Welcome to SALU Exam Portal.');
    }

    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|min:3|max:255',
            'father_name' => 'required|string|min:3|max:255',
            'cnic' => 'required|string|size:15|unique:users,cnic',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|size:12',
            'password' => ['required', 'string', PasswordRule::min(8)],
        ], [
            'cnic.size' => 'CNIC must contain 13 digits (format: 00000-0000000-0).',
            'phone.size' => 'Phone number must be 11 digits starting with 03 (format: 0300-0000000).',
        ]);

        // Normalize CNIC
        $cnicDigits = preg_replace('/\D/', '', $validated['cnic']);
        if (strlen($cnicDigits) !== 13) {
            return response()->json([
                'message' => 'CNIC must contain 13 digits.',
                'field' => 'cnic',
            ], 422);
        }

        // Normalize phone
        $phoneDigits = preg_replace('/\D/', '', $validated['phone']);
        if (strlen($phoneDigits) !== 11 || ! str_starts_with($phoneDigits, '03')) {
            return response()->json([
                'message' => 'Phone number must be 11 digits starting with 03.',
                'field' => 'phone',
            ], 422);
        }

        // Check for duplicate phone
        $formattedPhone = substr($phoneDigits, 0, 4).'-'.substr($phoneDigits, 4);
        if (User::wherePhoneDigits($phoneDigits)->exists()) {
            return response()->json([
                'message' => 'This phone number is already registered.',
                'field' => 'phone',
            ], 422);
        }

        $verificationCode = rand(100000, 999999);

        $user = User::create([
            'full_name' => trim($validated['full_name']),
            'father_name' => trim($validated['father_name']),
            'cnic' => $validated['cnic'],
            'email' => strtolower(trim($validated['email'])),
            'phone' => $formattedPhone,
            'password' => $validated['password'],
            'role' => 'STUDENT',
            'verification_code' => $verificationCode,
            'is_verified' => true, // Auto-verify for now
        ]);

        // Send verification email alert via Resend
        try {
            app(\App\Services\EmailService::class)->sendVerificationEmail(
                $user->email,
                $user->full_name,
                (string) $verificationCode
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Verification email sending error: '.$e->getMessage());
        }

        return response()->json([
            'message' => 'Registration successful. You can now sign in with your email or CNIC.',
        ], 201);
    }

    /**
     * Show the unified login board for all users
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Web login handler with automatic role detection and redirect
     */
    public function webLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ], [
            'email.required' => 'Please enter your CNIC or registered email address.',
            'password.required' => 'Please enter your password.',
        ]);

        $input = trim($credentials['email']);

        // Find user by email or CNIC using scope
        $user = User::whereEmailOrCnic($input)->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'email' => 'Invalid CNIC/Email or password.',
            ])->withInput($request->except('password'));
        }

        if (! $user->is_verified) {
            $user->is_verified = true;
            $user->save();
        }

        Auth::login($user, $request->filled('remember'));
        $request->session()->regenerate();

        return $this->redirectBasedOnRole($user)
            ->with('success', "Welcome back, {$user->full_name}!");
    }

    /**
     * Show force password change form
     */
    public function showForceChangePasswordForm()
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->must_change_password) {
            return $this->redirectBasedOnRole($user);
        }

        return view('auth.force-change-password', compact('user'));
    }

    /**
     * Handle force password change submission
     */
    public function forceChangePassword(Request $request)
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'string', 'confirmed', PasswordRule::min(8)],
        ], [
            'current_password.required' => 'Please enter your current temporary/default password.',
            'password.required' => 'Please enter a new password.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min' => 'Your new password must be at least 8 characters long.',
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'The current password you entered is incorrect.',
            ]);
        }

        if (Hash::check($validated['password'], $user->password)) {
            return back()->withErrors([
                'password' => 'The new password cannot be identical to your current default password.',
            ]);
        }

        $user->password = $validated['password'];
        $user->must_change_password = false;
        $user->password_changed_at = now();
        $user->save();

        try {
            \App\Models\AuditLog::create([
                'id' => (string) Str::uuid(),
                'user_id' => $user->id,
                'action' => 'FORCE_PASSWORD_CHANGED',
                'description' => "User {$user->email} successfully updated required initial password.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Non-fatal audit log
        }

        return $this->redirectBasedOnRole($user)
            ->with('success', 'Your password has been changed successfully! You now have full access.');
    }

    /**
     * API Force change password
     */
    public function apiForceChangePassword(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'string', 'confirmed', PasswordRule::min(8)],
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'The current password you entered is incorrect.',
                'errors' => ['current_password' => ['The current password is incorrect.']],
            ], 422);
        }

        if (Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'The new password cannot be identical to your current default password.',
                'errors' => ['password' => ['New password must be different.']],
            ], 422);
        }

        $user->password = $validated['password'];
        $user->must_change_password = false;
        $user->password_changed_at = now();
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.',
            'must_change_password' => false,
        ]);
    }

    /**
     * Helper to determine dashboard redirect based on role
     */
    public function redirectBasedOnRole(User $user)
    {
        if ($user->must_change_password) {
            return redirect()->route('password.force_change')
                ->with('warning', 'Security requirement: You must change your default password before proceeding.');
        }

        return match ($user->role) {
            'SUPERADMIN', 'ADMIN', 'COLLEGE_ADMIN' => redirect()->intended(route('admin.dashboard')),
            'STUDENT' => redirect()->intended(route('student.dashboard')),
            default => redirect()->intended(route('home')),
        };
    }

    /**
     * Web logout handler
     */
    public function webLogout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been successfully logged out.');
    }

    /**
     * API Login user with automatic role redirection path
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $input = trim($validated['email']);

        // Find user by email or CNIC using scope
        $user = User::whereEmailOrCnic($input)->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid email/CNIC or password.',
            ], 401);
        }

        // Auto-verify if not verified
        if (! $user->is_verified) {
            $user->is_verified = true;
            $user->save();
        }

        // Create token for API (Sanctum)
        $token = $user->createToken('auth-token')->plainTextToken;

        // Also log in via session for web requests
        Auth::login($user, $request->filled('remember'));

        $redirectPath = match (true) {
            $user->must_change_password => '/force-change-password',
            $user->role === 'SUPERADMIN' => '/admin/superadmin-dashboard',
            in_array($user->role, ['ADMIN', 'COLLEGE_ADMIN']) => '/admin/dashboard',
            $user->role === 'STUDENT' => '/student/dashboard',
            default => '/',
        };

        return response()->json([
            'token' => $token,
            'redirect_url' => $redirectPath,
            'must_change_password' => (bool) $user->must_change_password,
            'user' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'role' => $user->role,
                'phone' => $user->phone,
                'must_change_password' => (bool) $user->must_change_password,
            ],
        ]);
    }

    /**
     * Get current authenticated user
     */
    public function me(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'User not authenticated.',
            ], 401);
        }

        $user->load('college');

        return response()->json([
            'id' => $user->id,
            'full_name' => $user->full_name,
            'father_name' => $user->father_name,
            'email' => $user->email,
            'cnic' => $user->cnic,
            'role' => $user->role,
            'phone' => $user->phone,
            'college' => $user->college ? [
                'id' => $user->college->id,
                'name' => $user->college->name,
            ] : null,
        ]);
    }

    /**
     * Recover email by CNIC lookup
     */
    public function recoverEmail(Request $request)
    {
        $validated = $request->validate([
            'cnic' => 'required|string',
        ]);

        $cnicDigits = preg_replace('/\D/', '', $validated['cnic']);

        if (strlen($cnicDigits) !== 13) {
            return response()->json([
                'message' => 'CNIC must contain 13 digits (format: 00000-0000000-0).',
            ], 422);
        }

        $user = User::whereCnicDigits($cnicDigits)->first();

        if (! $user) {
            return response()->json([
                'found' => false,
                'message' => 'If an account exists with this CNIC, recovery instructions have been recorded.',
            ], 200);
        }

        $maskedEmail = $this->maskEmail($user->email);

        return response()->json([
            'found' => true,
            'full_name' => $user->full_name,
            'masked_email' => $maskedEmail,
        ]);
    }

    /**
     * Forgot password - send reset link
     */
    public function forgotPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', strtolower(trim($validated['email'])))->first();

        if ($user) {
            // Generate cryptographically secure reset token (64 hex characters from 32 random bytes)
            $token = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $token);

            $user->password_reset_token_hash = $tokenHash;
            $user->password_reset_token_expires_at = now()->addMinutes(15);
            $user->save();

            // Build reset link
            $resetLink = url("/reset-password?token={$token}&email=".urlencode($user->email));

            // Send email via Resend Email Service
            try {
                app(\App\Services\EmailService::class)->sendPasswordResetEmail(
                    $user->email,
                    $user->full_name,
                    $resetLink
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Password reset email sending error: '.$e->getMessage());
            }
        }

        return response()->json([
            'message' => 'If your email is registered, a reset link has been sent.',
        ]);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => ['required', 'string', PasswordRule::min(8)],
        ]);

        $tokenHash = hash('sha256', $validated['token']);
        $user = User::where('email', strtolower(trim($validated['email'])))->first();

        $isValid = $user
            && ! empty($user->password_reset_token_hash)
            && $user->password_reset_token_expires_at
            && ! $user->password_reset_token_expires_at->isPast()
            && hash_equals($user->password_reset_token_hash, $tokenHash);

        if (! $isValid) {
            return response()->json([
                'message' => 'Reset link is invalid or has expired.',
            ], 400);
        }

        $user->password = $validated['password'];
        $user->password_reset_token_hash = null;
        $user->password_reset_token_expires_at = null;
        $user->save();

        return response()->json([
            'message' => 'Password has been reset. You can now login.',
        ]);
    }

    /**
     * Verify reset token
     */
    public function verifyResetToken(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
        ]);

        $tokenHash = hash('sha256', $validated['token']);
        $user = User::where('email', strtolower(trim($validated['email'])))->first();

        $isValid = $user
            && ! empty($user->password_reset_token_hash)
            && $user->password_reset_token_expires_at
            && ! $user->password_reset_token_expires_at->isPast()
            && hash_equals($user->password_reset_token_hash, $tokenHash);

        return response()->json(['valid' => (bool) $isValid]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        // Revoke all tokens for API
        $request->user()->tokens()->delete();

        // Logout from session
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Successfully logged out.',
        ]);
    }

    /**
     * Mask email for privacy
     */
    private function maskEmail(string $email): string
    {
        if (empty($email)) {
            return '';
        }

        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return $email;
        }

        $name = $parts[0];
        $domain = $parts[1];

        if (strlen($name) <= 2) {
            return $name[0].'*@'.$domain;
        }

        $maskedName = $name[0].str_repeat('*', min(strlen($name) - 2, 5)).$name[strlen($name) - 1];

        return $maskedName.'@'.$domain;
    }
}
