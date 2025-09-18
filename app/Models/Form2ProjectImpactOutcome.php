<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form2ProjectImpactOutcome extends Model
{
    use HasFactory;
    protected $table = 'form2_project_impact_outcomes';
    protected $fillable = [
        'form2_project_proposals_id',
        'impact',
        'outcome',
        'linkage',
    ];

    public function proposal() {
        return $this->belongsTo(Form2ProjectProposal::class, 'form2_project_proposals_id');
    }
}
