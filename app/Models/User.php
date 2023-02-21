<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens,
        HasFactory,
        Notifiable,
        HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'full_name',
        'user_mobile',
        'role',
        'avatar',
        'company_mobile',
        'company_name',
        'company_address',
        'company_description',
        'parent_id',
        'creator_id',
        'active',
        'company_logo',
        'is_mangement_team'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }
    public function creator()
    {
        return $this->hasMany(User::class, 'creator_id');
    }
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Get all of the projects owner for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projectsOwner(): HasMany
    {
        return $this->hasMany(Project::class, 'project_owner', 'id');
    }
    /**
     * Get all of the projectCreator for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projectCreator(): HasMany
    {
        return $this->hasMany(Project::class, 'creator_id', 'id');
    }
}
