<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramTeamMember extends Model
{
    use HasFactory;
    protected $table = 'program_team_members';
    protected $fillable = [
        'program_proposals_id',
        'name'
    ];

    public function ProgramProposal() {
        return $this->belongsTo(ProgramProposal::class, 'program_proposals_id');
    }
}
