<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramActivityPlan extends Model
{
    use HasFactory;
    protected $table = 'program_activity_plans';
    protected $fillable = [
        'program_proposals_id',
        'activity',
        'outputs',
        'timeline',
        'personnel'
    ];

    public function ProgramProposal() {
        return $this->belongsTo(ProgramProposal::class, 'program_proposals_id');
    }
}
