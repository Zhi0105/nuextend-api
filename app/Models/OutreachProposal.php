<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutreachProposal extends Model
{
    use HasFactory;

    protected $table = 'outreach_proposals';
    protected $fillable = [
        'title',
        'description',
        'targetGroup',
        'startDate',
        'endDate',
        'projectLeader',
        'mobile',
        'email',
    ];

    public function OutreachActivityPlansBudgets() {
        return $this->hasMany(OutreachActivityPlansBudget::class, 'outreach_proposals_id');
    }
    public function OutreachDetailedBudgets() {
        return $this->hasMany(OutreachDetailedBudget::class, 'outreach_proposals_id');
    }
    public function OutreachBudgetSourcings() {
        return $this->hasMany(OutreachBudgetSourcing::class, 'outreach_proposals_id');
    }
}
