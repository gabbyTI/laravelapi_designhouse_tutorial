<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{

    use AuthenticatesUsers;

    protected function attemptLogin(Request $request)
    {
        //attempt to issue a token to the user based on their login credentials
        $token = $this->guard()->attempt($this->credentials($request));

        if (!$token) {
            return false;
        }

        //get auth user

        $user = $this->guard()->user();

        if ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()) {
            return false;
        }

        $this->guard()->setToken($token);

        return true;
    }

    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        //get token
        $token = (string)$this->guard()->getToken();

        //get expirydate
        $expiration = $this->guard()->getPayload()->get('exp');

        return response()->json([
            'token' => $token,
            'toke_type' => 'bearer',
            'expires_in' => $expiration
        ]);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $user = $this->guard()->user();

        if ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()) {
            return response()->json(["errors" => ["verification" => "You need to verify your email account"]]);
        }

        throw ValidationException::withMessages([
            $this->username() => "Invalid Credentials",
        ]);
    }

    protected function logout(Request $request)
    {
        $user = $this->guard()->logout();

        return response()->json(["message" => "Logged out successfully"]);
    }
}
