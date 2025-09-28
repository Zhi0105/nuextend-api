<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form12Attendee extends Model
{
    use HasFactory;

    protected $table = 'form12_attendees';

    protected $fillable = [
        'form12_id',
        'full_name',
        'designation',
        'department_id',
        'programs_id',
    ];

    public function form12()
    {
        return $this->belongsTo(Form12::class, 'form12_id');
}

    public function program()
    {
        return $this->belongsTo(Program::class, 'programs_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
