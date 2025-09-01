<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectProposal extends Model
{
    use HasFactory;
    protected $table = 'project_proposals';
    protected $fillable = [
        'event_type_id',
        'projectTitle',
        'proponents',
        'collaborators',
        'participants',
        'partners',
        'implementationDate',
        'durationHours',
        'area',
        'budgetRequirement',
        'budgetRequested',
        'background',
        'otherInfo',
        'projectLeader',
        'mobile',
        'email'
    ];

    public function EventType() {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }
    public function ProjectObjectives() {
        return $this->hasMany(ProjectObjective::class, 'project_proposals_id');
    }
    public function ProjectImpactOutcomes() {
        return $this->hasMany(ProjectImpactOutcome::class, 'project_proposals_id');
    }
    public function ProjectRisks() {
        return $this->hasMany(ProjectRisk::class, 'project_proposals_id');
    }
    public function ProjectStaffings() {
        return $this->hasMany(ProjectStaffing::class, 'project_proposals_id');
    }
    public function ProjectWorkPlans() {
        return $this->hasMany(ProjectWorkPlan::class, 'project_proposals_id');
    }
    public function ProjectDetailedBudgets() {
        return $this->hasMany(ProjectDetailedBudget::class, 'project_proposals_id');
    }
}
