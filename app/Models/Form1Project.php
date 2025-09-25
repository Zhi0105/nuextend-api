<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form1Project extends Model
{
    use HasFactory;
    protected $table = 'form1_projects';
    protected $fillable = [
    'form1_program_proposals_id',
    'title',
    'teamLeader', // keep camelCase per schema; or rename to team_leader in both migration & here
    'objectives',
    ];


    public function proposal() {
        return $this->belongsTo(Form1ProgramProposal::class, 'form1_program_proposals_id');
    }
    public function teamMembers() {
        return $this->hasMany(Form1ProjectTeamMember::class, 'form1_projects_id');
    }
    public function budgetSummaries() {
        return $this->hasMany(Form1BudgetSummary::class, 'form1_projects_id');
    }
}
