<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramComponentProject extends Model
{
    use HasFactory;
    protected $table = 'program_component_projects';
    protected $fillable = [
        'program_proposals_id',
        'componentProjectTitle',
        'outcomes',
        'budget'
    ];

    public function ProgramProposal() {
        return $this->belongsTo(ProgramProposal::class, 'program_proposals_id');
    }

}
