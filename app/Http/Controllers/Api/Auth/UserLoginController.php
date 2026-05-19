<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UserLoginController extends Controller
{
    /**
     * Login user
     */
    public function login(LoginRequest $request)
    {
        // Attempt login
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials'],
            ]);
        }

        $user = Auth::user();

        // Optional: limit active sessions/tokens
        if (!$this->checkUsersActiveSession($user->email)) {
            return response()->error(
                'You already have 3 active sessions.',
                [],
                422
            );
        }

        // Create Passport token
        $token = $user->createToken('user-api')->accessToken;

        return response()->success('Loggedin Successfully.', [
            'token' => $token,
            'user' => $user,
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user && $user->token()) {
            $user->token()->revoke();
        }

        return response()->success('Logged out successfully', []);
    }

    /**
     * Limit active sessions
     */
    public function checkUsersActiveSession(string $email)
    {
        $user = User::withCount([
            'tokens' => function ($query) {
                $query->where('revoked', 0);
            }
        ])->where('email', $email)->first();

        return $user->tokens_count < 3;
    }
}
