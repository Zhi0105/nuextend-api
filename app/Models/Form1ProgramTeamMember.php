<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form1ProgramTeamMember extends Model
{
    use HasFactory;
    protected $table = 'form1_program_team_members';
    protected $fillable = [
        'form1_program_proposals_id',
        'name'
    ];

    public function proposal() {
        return $this->belongsTo(Form1ProgramProposal::class, 'form1_program_proposals_id');
    }
}
