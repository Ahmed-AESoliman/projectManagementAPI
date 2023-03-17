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
        'parent_id',
        'is_mangement_team',
        'company_owner',
        'company_id'
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
    public function company()
    {
        return $this->belongsTo(User::class, 'company_id');
    }
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }


    public function projectsOwner(): HasMany
    {
        return $this->hasMany(Project::class, 'project_owner', 'id');
    }

    public function projectCreator(): HasMany
    {
        return $this->hasMany(Project::class, 'creator_id', 'id');
    }

    public function companyOwner(): HasMany
    {
        return $this->hasMany(Employee::class, 'company', 'id');
    }

    public function employeeCreator(): HasMany
    {
        return $this->hasMany(Employee::class, 'creator_id', 'id');
    }
}
