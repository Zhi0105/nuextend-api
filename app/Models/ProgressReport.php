<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgressReport extends Model
{
    use HasFactory;

    protected $table = 'progress_reports';
    protected $fillable = [
        'event_id',
        'activity_id',
        'name',
        'file',
        'date',
        'budget',
        'is_commex',
        'is_asd',
        'commex_remarks',
        'asd_remarks',
        'commex_approve_date',
        'asd_approve_date'
    ];

    public function event() {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function activity() {
        return $this->belongsTo(Activity::class, 'activity_id');
    }
}
