<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form2ProjectDetailedBudget extends Model
{
    use HasFactory;
    protected $table = 'form2_project_detailed_budgets';

    protected $fillable = [
        'form2_project_proposals_id',
        'item',
        'description',
        'quantity',
        'amount',
        'source',
    ];

    public function proposal() {
        return $this->belongsTo(Form2ProjectProposal::class, 'form2_project_proposals_id');
    }
}
