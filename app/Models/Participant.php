<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Participant extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'participants';
    protected $fillable = [
        'user_id',
        'event_id',
        'is_attended'
    ];

    protected $dates = ['deleted_at'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function event() {
        return $this->belongsTo(Event::class, 'event_id');
    }
    public function attendance() {
        return $this->hasMany(Attendance::class, 'participant_id');
    }
}
