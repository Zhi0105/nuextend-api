<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramProjectTeamMember extends Model
{
    use HasFactory;
    protected $table = 'program_project_team_members';
    protected $fillable = [
        'program_project_id',
        'name',
    ];

    public function ProgramProject() {
        return $this->belongsTo(ProgramProject::class, 'program_project_id');
    }
}
