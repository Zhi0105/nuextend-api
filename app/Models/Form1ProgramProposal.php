<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form1ProgramProposal extends Model
{
    use HasFactory;
    protected $table = 'form1_program_proposals';
    protected $fillable = [
        'duration',
        'background',
        'overall_goal',
        'scholarly_connection'
    ];

        // Relationships
    public function teamMembers() {
        return $this->hasMany(Form1ProgramTeamMember::class, 'form1_program_proposals_id');
    }
    public function cooperatingAgencies() {
        return $this->hasMany(Form1CooperatingAgency::class, 'form1_program_proposals_id');
    }
    public function componentProjects() {
        return $this->hasMany(Form1ComponentProject::class, 'form1_program_proposals_id');
    }
    public function projects() {
        return $this->hasMany(Form1Project::class, 'form1_program_proposals_id');
    }
    public function budgetSummaries() {
        return $this->hasMany(Form1BudgetSummary::class, 'form1_program_proposals_id');
    }

    public function commexApprover() {
        return $this->belongsTo(User::class, 'commex_approved_by')->withDefault();
    }
    public function deanApprover() {
        return $this->belongsTo(User::class, 'dean_approved_by')->withDefault();
    }
    public function asdApprover() {
        return $this->belongsTo(User::class, 'asd_approved_by')->withDefault();
    }
    public function adApprover() {
        return $this->belongsTo(User::class, 'ad_approved_by')->withDefault();
    }

}
