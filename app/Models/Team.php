<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'owner_id',
        'slug'
    ];

    protected static function boot()
    {
        parent::boot();

        /**
         * listens to when a create method is called on this model.
         * Therefore, when team is created add current user as team member
         *  */
        static::created(function ($team) {
            // auth()->user()->teams()->attach($team->id);
            $team->members()->attach(auth()->id());
        });

        static::deleted(function ($team) {
            $team->members->sync([]);
        });
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function hasPendingInvite($email)
    {
        return (bool)$this->invitations()->where('recipient_email', $email)->count();
    }

    public function members()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function designs()
    {
        return $this->hasMany(Design::class);
    }

    public function hasUser(User $user)
    {
        return $this->members()->where('user_id', $user->id)->first() ? true : false;
    }
}
