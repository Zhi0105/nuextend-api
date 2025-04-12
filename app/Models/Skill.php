<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Skill extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'skills';
    protected $fillable = ['name'];

    protected $dates = ['deleted_at'];

    public function users() {
        return $this->belongsToMany(User::class, 'user_skills')
                    ->withTimestamps();
    }
    public function events() {
        return $this->belongsToMany(Event::class, 'event_skills', 'skill_id', 'event_id')
                    ->withTimestamps();
    }
}
