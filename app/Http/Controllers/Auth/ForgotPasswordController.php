<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    protected function sendResetLinkResponse(Request $request, $response)
    {
        return response()->json(["message" => trans($response)]);
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        throw ValidationException::withMessages([
            'email' => [trans($response)],
        ]);
    }
}
