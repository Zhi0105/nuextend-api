<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutreachDetailedBudget extends Model
{
    use HasFactory;
    protected $table = 'outreach_detailed_budgets';
    protected $fillable = [
        'outreach_proposals_id',
        'item',
        'details',
        'quantity',
        'amount',
        'total'
    ];

    public function OutreachProposal() {
        return $this->belongsTo(OutreachProposal::class, 'outreach_proposals_id');
    }
}
