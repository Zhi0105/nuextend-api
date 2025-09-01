<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramProject extends Model
{
    use HasFactory;
    protected $table = 'program_projects';
    protected $fillable = [
        'program_proposals_id',
        'projectTitle',
        'teamLeader',
        'objectives'
    ];

    public function ProgramProjectTeamMembers() {
        return $this->hasMany(ProgramProjectTeamMember::class, 'program_project_id');
    }
    public function ProgramProposal() {
        return $this->belongsTo(ProgramProposal::class, 'program_proposals_id');
    }
}
