<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectImpactOutcome extends Model
{
    use HasFactory;
    protected $table = 'project_impact_outcomes';
    protected $fillable = [
        'project_proposals_id',
        'impact',
        'outcome',
        'linkage'
    ];

    public function ProjectProposal() {
        return $this->belongsTo(ProjectProposal::class, 'project_proposals_id');
    }
}
