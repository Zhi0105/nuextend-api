<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form3OutreachDetailedBudget extends Model
{
    use HasFactory;
    protected $table = 'form3_outreach_detailed_budgets';

    protected $fillable = [
        'form3_outreach_proposals_id',
        'item',
        'details',
        'quantity',
        'amount',
        'total',
    ];

    // Relationship
    public function proposal() {
        return $this->belongsTo(Form3OutreachProposal::class, 'form3_outreach_proposals_id');
    }
}
