<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectRisk extends Model
{
    use HasFactory;
    protected $table = 'project_risks';
    protected $fillable = [
        'project_proposals_id',
        'risk',
        'mitigation'
    ];

    public function ProjectProposal() {
        return $this->belongsTo(ProjectProposal::class, 'project_proposals_id');
    }
}
