<?php

namespace App\Http\Controllers;

use App\Mail\sendInvitationToJoinTeam;
use App\Models\Invitation;
use App\Models\Team;
use App\Repositories\Contracts\IInvitation;
use App\Repositories\Contracts\ITeam;
use App\Repositories\Contracts\IUser;
use Illuminate\Http\Request;
use Mail;

class InvitationsController extends Controller
{
    protected $invitations;
    protected $teams;
    protected $users;

    public function __construct(IInvitation $invitations, ITeam $teams, IUser $users)
    {
        $this->invitations = $invitations;
        $this->teams = $teams;
        $this->users = $users;
    }

    public function invite(Request $request, $teamId)
    {
        $team = $this->teams->find($teamId);

        $this->validate($request, [
            'email' => ['required', 'email']
        ]);

        $user = auth()->user();
        //check if user is owner of team
        if (!$user->isOwnerOfTeam($team)) {
            return response()->json([
                "email" => "you are not the team owner"
            ], 401);
        }
        //check if invite has already been sent to email
        if ($team->hasPendingInvite($request->email)) {
            return response()->json([
                'email' => 'Email already has a pending invite'
            ], 422);
        }


        $recipient = $this->users->findByEmail($request->email);
        // check if recipient is not a regitered member and if not send a registration invitation
        if (!$recipient) {
            $this->createInvitation(false, $team, $request->email);
            return response()->json([
                "message" => "Invitation sent successfully"
            ]);
        }

        //check if recipient is already a team member
        if ($team->hasUser($recipient)) {
            return response()->json([
                "email" => "This user is already a team member"
            ], 422);
        }

        //send the team invitation
        $this->createInvitation(true, $team, $request->email);

        return response()->json([
            "message" => "Invitation sent successfully"
        ]);
    }


    public function resend($id)
    {
        # code...
        $invitation = $this->invitations->find($id);

        $this->authorize('resend', $invitation);

        $recipient = $this->users->findByEmail($invitation->recipient_email);

        $this->resendInvitation($invitation, !is_null($recipient));

        return response()->json([
            "message" => "Invitation resent successfully"
        ]);
    }

    public function respond(Request $request, $id)
    {
        $this->validate($request, [
            'token' => ['required'],
            'decision' => ['required']
        ]);

        $token = $request->token;
        $decision = $request->decision; // accept or deny
        $invitation = $this->invitations->find($id);

        // check if invitation belongs to them

        $this->authorize('respond', $invitation);

        ///check to make sure tokens match

        if ($invitation->token != $token) {
            return response()->json([
                "message" => "Invalid Token"
            ], 401);
        }

        if ($decision != 'deny') {
            $this->invitations->addUserToTeam($invitation->team, auth()->id());
        }

        $invitation->delete();

        return response()->json([
            "message" => "Successful"
        ]);
    }

    public function destroy($id)
    {
        $invitation = $this->invitations->find($id);
        $this->authorize('delete', $invitation);

        $invitation->delete();

        return response()->json([
            "message" => "Deleted"
        ]);
    }

    protected function createInvitation(bool $user_exists, Team $team, string $email)
    {
        $invitation = $this->invitations->create([
            'team_id' => $team->id,
            'sender_id' => auth()->id(),
            'recipient_email' => $email,
            'token' => md5(uniqid(microtime()))
        ]);

        Mail::to($email)->send(new sendInvitationToJoinTeam($invitation, $user_exists));
    }

    protected function resendInvitation(Invitation $invitation, bool $user_exists)
    {
        Mail::to($invitation->recipient_email)->send(new sendInvitationToJoinTeam($invitation, $user_exists));
    }
}
