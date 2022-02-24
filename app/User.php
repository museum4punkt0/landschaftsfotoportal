<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'group_fk',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    
    /**
     * Get the group that owns the user.
     */
    public function group()
    {
        return $this->belongsTo('App\Group', 'group_fk', 'group_id');
    }
    
    
    /**
     * Scope a query to only include users who have moderation privileges.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeModerators($query)
    {
        return $query->whereHas('group', function ($query) {
            $query->where('config->is_moderator', true);
        });
    }


    /**
     * Checks if the user belongs to the group.
     */
    public function inGroup(string $group)
    {
        return $this->group->name == $group;
    }
    
    /**
     * Checks if the user's edits are beeing moderated.
     */
    public function isModerated()
    {
        return $this->group->config['is-moderated'] ?? false;
    }
    
    /**
     * Checks if the user is allowed to access.
     */
    public function hasAccess(array $permissions)
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Checks if the user's group' has the given permission.
     */
    private function hasPermission(string $permission)
    {
        return $this->group->permissions[$permission] ?? false;
    }
}
