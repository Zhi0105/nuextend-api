<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\CustomVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
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
        'email_verified_at',
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
    public function commexApprovedForms() {
        return $this->hasOne(Form::class, 'commex_approved_by');
    }
    public function deanApprovedForms() {
        return $this->hasMany(Form::class, 'dean_approved_by');
    }
    public function asdApprovedForms() {
        return $this->hasMany(Form::class, 'asd_approved_by');
    }
    public function adApprovedForms() {
        return $this->hasMany(Form::class, 'ad_approved_by');
    }
    public function sendEmailVerificationNotification() {
        $this->notify(new CustomVerifyEmail());
    }
}
