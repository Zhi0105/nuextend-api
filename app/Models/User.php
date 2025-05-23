<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'department_id',
        'program_id',
        'role_id',
        'school_id',
        'firstname',
        'middlename',
        'lastname',
        'email',
        'password',
        'contact',
        'status',
        'is_EsVolunteer'
    ];

    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function department() {
        return $this->belongsTo(Department::class, 'department_id');
    }
    public function program() {
        return $this->belongsTo(Program::class, 'program_id');
    }
    public function role() {
        return $this->belongsTo(Role::class, 'role_id');
    }
    public function skill() {
        return $this->belongsToMany(Skill::class, 'user_skills')->withTimestamps();
    }
    public function organizations() {
        return $this->belongsToMany(Organization::class, 'organization_members')
                    ->withPivot('role_id', 'created_at', 'updated_at')
                    ->withTimestamps();
    }
    public function events() {
        return $this->hasMany(Event::class, 'user_id');
    }
    public function participants() {
        return $this->hasMany(Participant::class, 'user_id');
    }
}
