<?php

namespace App\Http\Controllers;

use App\Http\Resources\TeamResource;
use App\Models\Team;
use App\Repositories\Contracts\ITeam;
use App\Repositories\Contracts\IUser;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use Illuminate\Http\Request;
use Str;

class TeamsController extends Controller
{
    protected $teams;
    protected $users;

    public function __construct(ITeam $teams, IUser $users)
    {
        $this->teams = $teams;
        $this->users = $users;
    }

    public function index(Request $request)
    {
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:80', 'unique:teams,name'],
        ]);

        $team = $this->teams->create(
            [
                'owner_id' => auth()->id(),
                'name' => $request->name,
                'slug' => Str::slug($request->name)
            ]
        );

        // current user is inserted as team member using the boot methodin Team model

        return new TeamResource($team);
    }
    public function update(Request $request, $id)
    {
        $team = $this->teams->find($id);
        $this->authorize('update', $team);

        $request->validate([
            'name' => ['required', 'string', 'max:80', 'unique:teams,name,' . $id],
        ]);

        $team = $this->teams->update($id, [
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return new TeamResource($team);
    }

    public function findById(Request $request, $id)
    {
        $team = $this->teams->find($id);
        return new TeamResource($team);
    }

    /**
     * Get teams the current user belongs to
     */
    public function fetchUserTeams(Request $request)
    {
        $teams = $this->teams->fetchUserTeams();
        return TeamResource::collection($teams);
    }
    public function findBySlug(Request $request)
    {
    }
    public function destroy(Request $request, $id)
    {
        $team = $this->teams->find($id);
        $this->authorize('delete', $team);

        $team->delete($id);

        return response()->json([
            "message" => "deleted successfully"
        ]);
    }

    public function removeFromTeam($teamId, $userId)
    {
        // get the team
        $team = $this->teams->find($teamId);
        //get the user
        $user = $this->users->find($userId);

        if (!auth()->user()->isOwnerOfTeam($team) && auth()->id() != $user->id) {
            if ($user->isOwnerOfTeam($team)) {
                return response()->json([
                    "message" => "You cannot do this"
                ], 401);
            }
        }

        if ($user->isOwnerOfTeam($team)) {
            return response()->json([
                "message" => "This is your team"
            ], 401);
        }

        $this->teams->removeUserFromTeam($team, $userId);

        return response()->json([
            "message" => "Success"
        ]);
    }
}
