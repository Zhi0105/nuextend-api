<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form2ProjectRisk extends Model
{
    use HasFactory;
    protected $table = 'form2_project_risks';

    protected $fillable = [
        'form2_project_proposals_id',
        'risk_identification',
        'risk_mitigation',
    ];

    public function proposal() {
        return $this->belongsTo(Form2ProjectProposal::class, 'form2_project_proposals_id');
    }
}
