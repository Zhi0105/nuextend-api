<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form3OutreachBudgetSourcing extends Model
{
    use HasFactory;
    protected $table = 'form3_outreach_budget_sourcings';

    protected $fillable = [
        'form3_outreach_proposals_id',
        'university',
        'outreachGroup',
        'service',
        'other',
        'total',
    ];

    // Relationship
    public function proposal() {
        return $this->belongsTo(Form3OutreachProposal::class, 'form3_outreach_proposals_id');
    }
}
