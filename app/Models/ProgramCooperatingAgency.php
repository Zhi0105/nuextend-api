<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramCooperatingAgency extends Model
{
    use HasFactory;
    protected $table = 'program_cooperating_agencies';
    protected $fillable = [
        'program_proposals_id',
        'name'
    ];

    public function ProgramProposal() {
        return $this->belongsTo(ProgramProposal::class, 'program_proposals_id');
    }
}
