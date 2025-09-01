<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramProposal extends Model
{
    use HasFactory;
    protected $table = 'program_proposals';
    protected $fillable = [
        'title',
        'implementer',
        'targetGroup',
        'duration',
        'proposalBudget',
        'background',
        'overallGoal',
        'scholarlyConnection',
        'coordinator',
        'mobileNumber',
        'email'
    ];

    public function ProgramTeamMembers() {
        return $this->hasMany(ProgramTeamMember::class, 'program_proposals_id');
    }
    public function ProgramCooperatingAgencies() {
        return $this->hasMany(ProgramCooperatingAgency::class, 'program_proposals_id');
    }
    public function ProgramComponentProjects() {
        return $this->hasMany(ProgramComponentProject::class, 'program_proposals_id');
    }
    public function ProgramProjects() {
        return $this->hasMany(ProgramProject::class, 'program_proposals_id');
    }
    public function ProgramActivityPlans() {
        return $this->hasMany(ProgramActivityPlan::class, 'program_proposals_id');
    }
}
