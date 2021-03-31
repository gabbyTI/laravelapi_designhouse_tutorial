<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Design\CommentController;
use App\Http\Controllers\Design\DesignController;
use App\Http\Controllers\Design\UploadController;
use App\Http\Controllers\InvitationsController;
use App\Http\Controllers\TeamsController;
use App\Http\Controllers\User\MeController;
use App\Http\Controllers\User\SettingsController;
use App\Http\Controllers\User\UserController;

// PUBLIC ROUTES

Route::get('me', [MeController::class, 'getMe']);

// Get Designs
Route::get('designs', [DesignController::class, 'index']);
Route::get('designs/{id}', [DesignController::class, 'findDesign']);

// Get Users
Route::get('users', [UserController::class, 'index']);

//Teams
Route::get('teams/slug/{slug}', [TeamsController::class, 'findBySlug']);

// Route group for authenticated users only
Route::group(['middleware' => ['auth:api']], function () {
    Route::post('logout', [LoginController::class, 'logout']);
    Route::put('settings/profile', [SettingsController::class, 'updateProfile']);
    Route::put('settings/password', [SettingsController::class, 'updatePassword']);

    //Upload Designs
    Route::post('designs', [UploadController::class, 'upload']);
    Route::put('designs/{id}', [DesignController::class, 'update']);
    Route::delete('designs/{id}', [DesignController::class, 'destroy']);

    // Like and Unlike
    Route::post('designs/{id}/like', [DesignController::class, 'like']);
    Route::get('designs/{id}/liked', [DesignController::class, 'checkIfUserHasLiked']);

    //Comments
    Route::post('designs/{id}/comment ', [CommentController::class, 'store']);
    Route::put('comments/{id}', [CommentController::class, 'update']);
    Route::delete('designs/{id}/comments', [CommentController::class, 'destroy']);

    //Teams
    Route::post('teams', [TeamsController::class, 'store']);
    Route::get('teams/{id}', [TeamsController::class, 'findById']);
    Route::get('teams', [TeamsController::class, 'index']);
    Route::get('users/teams', [TeamsController::class, 'fetchUserTeams']);
    Route::put('teams/{id}', [TeamsController::class, 'update']);
    Route::delete('teams/{id}', [TeamsController::class, 'destroy']);
    Route::delete('team/{team_id}/user/{user_id}', [TeamsController::class, 'removeFromTeam']);

    //Invitation
    Route::post('invitation/{teamId}', [InvitationsController::class, 'invite']);
    Route::post('invitation/{id}/resend', [InvitationsController::class, 'resend']);
    Route::post('invitation/{id}/respond', [InvitationsController::class, 'respond']);
    Route::delete('invitation/{id}', [InvitationsController::class, 'destroy']);
});

// Route group for guest users only
Route::group(['middleware' => ['guest:api']], function () {
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('verification/verify/{user}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('verification/resend', [VerificationController::class, 'resend']);
    Route::post('login', [LoginController::class, 'login']);
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('password/reset', [ResetPasswordController::class, 'reset']);
});
