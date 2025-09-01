<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectObjective extends Model
{
    use HasFactory;
    protected $table = 'project_objectives';
    protected $fillable = [
        'project_proposals_id',
        'objective',
        'strategies',
    ];

    public function ProjectProposal() {
        return $this->belongsTo(ProjectProposal::class, 'project_proposals_id');
    }
}
