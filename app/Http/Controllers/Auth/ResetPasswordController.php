<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    protected function sendResetResponse(Request $request, $response)
    {
        return new JsonResponse(['message' => trans($response)]);
    }

    protected function sendResetFailedResponse(Request $request, $response)
    {
        throw ValidationException::withMessages([
            'email' => [trans($response)],
        ]);
    }
}
