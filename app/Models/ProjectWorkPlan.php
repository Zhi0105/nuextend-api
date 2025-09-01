<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectWorkPlan extends Model
{
    use HasFactory;
    protected $table = 'project_work_plans';
    protected $fillable = [
        'project_proposals_id',
        'phaseDate',
        'activities',
        'targets',
        'indicators',
        'personnel',
        'resources',
        'cost'
    ];

    public function ProjectProposal() {
        return $this->belongsTo(ProjectProposal::class, 'project_proposals_id');
    }

}
