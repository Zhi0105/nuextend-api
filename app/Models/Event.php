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
        'user_id',
        'organization_id',
        'name',
        'model_id',
        'event_type_id',
        'event_status_id',
        'target_group',
        'term',
        'budget_proposal',
        'is_posted',
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
        return $this->belongsToMany(Form::class, 'event_form')->withTimestamps();
    }
    // public function targetgroup() {
    //     return $this->belongsTo(Targetgroup::class, 'target_group_id');
    // }
    public function activities() {
        return $this->hasMany(Activity::class, 'event_id');
    }

// Optional BC alias (so old code using ->activity() still works)
    public function activity() {
        return $this->activities();
    }
    public function progress_report() {
        return $this->hasMany(ProgressReport::class, 'event_id');
    }



    public function form1() {
        return $this->hasMany(Form1ProgramProposal::class, 'event_id');
    }
    public function form2() {
        return $this->hasMany(Form2ProjectProposal::class, 'event_id');
    }
    public function form3() {
        return $this->hasMany(Form3OutreachProposal::class, 'event_id');
    }
    public function form4() {
        return $this->hasMany(Form4::class, 'event_id');
    }
    public function form5() {
        return $this->hasMany(Form5::class, 'event_id');
    }
    public function form6() {
        return $this->hasMany(Form6::class, 'event_id');
    }
    public function form7() {
        return $this->hasMany(Form7::class, 'event_id');
    }
    public function form8() {
        return $this->hasMany(Form8::class, 'event_id');
    }
    public function form9() {
        return $this->hasMany(Form9::class, 'event_id');
    }
    public function form10() {
        return $this->hasMany(Form10::class, 'event_id');
    }
    public function form11() {
        return $this->hasMany(Form11::class, 'event_id');
    }
}
