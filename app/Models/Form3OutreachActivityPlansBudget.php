<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form3OutreachActivityPlansBudget extends Model
{
    use HasFactory;
    protected $table = 'form3_outreach_activity_plans_budgets';
    protected $fillable = [
        'form3_outreach_proposals_id',
        'objectives',
        'activities',
        'outputs',
        'personnel',
        'budget'
    ];

    // Relationship
    public function proposal() {
        return $this->belongsTo(Form3OutreachProposal::class, 'form3_outreach_proposals_id');
    }
}
