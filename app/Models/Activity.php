<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;
    protected $table = 'activities';
    protected $fillable = [
        'event_id',
        'name',
        'address',
        'start_date',
        'end_date',
        'description'
    ];

    public function event() {
        return $this->belongsTo(Event::class, 'event_id');
    }
    public function progress_report() {
        return $this->hasMany(ProgressReport::class, 'activity_id');
    }

    public function form14() {
        return $this->hasMany(Form14::class, 'activities_id');
    }
    
}
