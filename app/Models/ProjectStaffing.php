<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectStaffing extends Model
{
    use HasFactory;
    protected $table = 'project_staffings';
    protected $fillable = [
        'project_proposals_id',
        'staff',
        'responsibilities',
        'contact'
    ];

    public function ProjectProposal() {
        return $this->belongsTo(ProjectProposal::class, 'project_proposals_id');
    }
}
