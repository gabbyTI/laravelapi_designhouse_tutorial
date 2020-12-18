<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Rules\CheckSamePassword;
use App\Rules\MatchOldPassword;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $this->validate($request, [
            'name' => ['required'],
            'tagline' => ['required'],
            'about' => ['required'],
            'formatted_address' => ['required'],
            'location.latitude' => ['required', 'numeric', 'min:-90', 'max:90'],
            'location.longitude' => ['required', 'numeric', 'min:-180', 'max:180'],
        ]);

        // dd($request);
        $location = new Point($request->location['latitude'], $request->location['longitude']);
        $user->update([
            'tagline' => $request->tagline,
            'name' => $request->name,
            'about' => $request->about,
            'formatted_address' => $request->formatted_address,
            'location' => $location,
            'available_to_hire' => $request->available_to_hire,
        ]);

        return new UserResource($user);
    }

    public function updatePassword(Request $request)
    {
        $this->validate($request, [
            'current_password' => ['required', new MatchOldPassword],
            'password' => ['required', 'confirmed', 'min:6', new CheckSamePassword],
        ]);

        $request->user()->update([
            'password' => bcrypt($request->password)
        ]);

        return response()->json([
            'message' => 'Password updated'
        ]);
    }
}
