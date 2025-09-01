<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectDetailedBudget extends Model
{
    use HasFactory;
    protected $table = 'project_detailed_budgets';
    protected $fillable = [
        'project_proposals_id',
        'item',
        'description',
        'quantity',
        'amount',
        'source',
    ];

    public function ProjectProposal() {
        return $this->belongsTo(ProjectProposal::class, 'project_proposals_id');
    }
}
