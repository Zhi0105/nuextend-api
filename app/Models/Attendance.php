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
        'is_attended'
    ];

    public function participant() {
        return $this->belongsTo(Participant::class, 'participant_id');
    }
}
