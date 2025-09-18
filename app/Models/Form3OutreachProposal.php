<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form3OutreachProposal extends Model
{
    use HasFactory;
    protected $table = 'form3_outreach_proposals';
    protected $fillable = [
        'description',
        'targetGroup',
        'startDate',
        'endDate'
    ];

    public function activityPlansBudgets() {
        return $this->hasMany(Form3OutreachActivityPlansBudget::class, 'form3_outreach_proposals_id');
    }
    public function detailedBudgets() {
        return $this->hasMany(Form3OutreachDetailedBudget::class, 'form3_outreach_proposals_id');
    }
    public function budgetSourcings() {
        return $this->hasMany(Form3OutreachBudgetSourcing::class, 'form3_outreach_proposals_id');
    }

    public function commexApprover() {
        return $this->belongsTo(User::class, 'commex_approved_by')->withDefault();
    }
    public function deanApprover() {
        return $this->belongsTo(User::class, 'dean_approved_by')->withDefault();
    }
    public function asdApprover() {
        return $this->belongsTo(User::class, 'asd_approved_by')->withDefault();
    }
    public function adApprover() {
        return $this->belongsTo(User::class, 'ad_approved_by')->withDefault();
    }
}
