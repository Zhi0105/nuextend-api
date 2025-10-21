<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'event_attendance';
    protected $fillable = [
        'participant_id',
        'attendance_date',
        'is_attended',
        'time_in',
        'time_out'
    ];

    protected $casts = [
        'time_in' => 'datetime',
        'time_out' => 'datetime',
    ];

    public function participant() {
        return $this->belongsTo(Participant::class, 'participant_id');
    }
}
