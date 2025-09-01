<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutreachBudgetSourcing extends Model
{
    use HasFactory;
    protected $table = 'outreach_budget_sourcings';
    protected $fillable = [
        'outreach_proposals_id',
        'university',
        'outreachGroup',
        'service',
        'other',
        'total'
    ];

    public function OutreachProposal() {
        return $this->belongsTo(OutreachProposal::class, 'outreach_proposals_id');
    }
}
