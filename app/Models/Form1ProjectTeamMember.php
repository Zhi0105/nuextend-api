<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form1ProjectTeamMember extends Model
{
    use HasFactory;
    protected $table = 'form1_project_team_members';
    protected $fillable = [
        'form1_projects_id',
        'name'
    ];


    public function project() {
        return $this->belongsTo(Form1Project::class, 'form1_projects_id');
    }
}
