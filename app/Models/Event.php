<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'events';
    protected $fillable = [
        'program_model_name',
        'user_id',
        'organization_id',
        'model_id',
        'event_type_id',
        'event_status_id',
        'target_group_id',
        'name',
        'term',
        'address',
        'start_date',
        'end_date',
        'remarks',
        'is_posted',
        'approve_date',
        'description'
    ];


    protected $dates = ['deleted_at'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function organization() {
        return $this->belongsTo(Organization::class, 'organization_id');
    }
    public function model() {
        return $this->belongsTo(Moddel::class, 'model_id');
    }
    public function eventtype() {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }
    public function eventstatus() {
        return $this->belongsTo(EventStatus::class, 'event_status_id');
    }
    public function skills() {
        return $this->belongsToMany(Skill::class, 'event_skills', 'event_id', 'skill_id')
                    ->withTimestamps();
    }
    public function eventmember() {
        return $this->hasMany(EventMember::class, 'event_id');
    }
    public function unsdgs() {
        return $this->belongsToMany(Unsdg::class, 'unsdg_lists')
                    ->withTimestamps();
    }
    public function participants() {
        return $this->hasMany(Participant::class, 'event_id');
    }
    public function forms() {
        return $this->hasMany(Form::class, 'event_id');
    }
    public function targetgroup() {
        return $this->belongsTo(Targetgroup::class, 'target_group_id');
    }
}
