<?php

namespace App\Repositories\Contracts;

interface IInvitation
{
    public function addUserToTeam($team, $user_id);
}
