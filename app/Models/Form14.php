<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form14 extends Model
{
    use HasFactory;

    protected $table = 'form14';
    protected $primaryKey = 'form14_id';

    protected $fillable = [
        'activities_id',
        'event_status_id',
        'objectives',
        'target_group',
        'description',
        'achievements',
        'challenges',
        'feedback',
        'acknowledgements',
        'is_commex',
        'is_asd',
        'commex_remarks',
        'asd_remarks',
        'commex_approved_by',
        'asd_approved_by',
        'commex_approve_date',
        'asd_approve_date',
    ];

    public function activities(){
        return $this->belongsTo(Activity::class, 'activities_id');
    }

    public function event_status(){
        return $this->belongsTo(EventStatus::class, 'event_status_id');
    }


    public function budgetSummaries(){
        return $this->hasMany(Form14BudgetSummary::class, 'form14_id', 'form14_id');
    }

    public function commexApprover(){
        return $this->belongsTo(User::class, 'commex_approved_by')->withDefault();
    }

    public function asdApprover(){
        return $this->belongsTo(User::class, 'asd_approved_by')->withDefault();
    }
} 
