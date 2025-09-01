<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutreachActivityPlansBudget extends Model
{
    use HasFactory;
    protected $table = 'outreach_activity_plans_budgets';
    protected $fillable = [
        'outreach_proposals_id',
        'objectives',
        'activities',
        'outputs',
        'personnel',
        'budget'
    ];

    public function OutreachProposal() {
        return $this->belongsTo(OutreachProposal::class, 'outreach_proposals_id');
    }

}
