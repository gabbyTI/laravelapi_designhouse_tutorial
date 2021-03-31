<?php

namespace App\Repositories\Eloquent;

use App\Models\Invitation;
use App\Repositories\Contracts\IInvitation;
use App\Repositories\Criteria\ICriteria;

class InvitationRepository  extends BaseRepository implements
    IInvitation
{
    public function model()
    {
        return Invitation::class;
    }

    public function addUserToTeam($team, $user_id)
    {
        $team->members()->attach($user_id);
    }
}
