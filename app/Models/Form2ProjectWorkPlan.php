<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form2ProjectWorkPlan extends Model
{
    use HasFactory;
    protected $table = 'form2_project_work_plans';
    protected $fillable = [
        'form2_project_proposals_id',
        'phaseDate',
        'activities',
        'targets',
        'indicators',
        'personnel',
        'resources',
        'cost',
    ];
    // If phaseDate is a real date:
    // protected $casts = ['phaseDate' => 'date'];

    public function proposal() {
        return $this->belongsTo(Form2ProjectProposal::class, 'form2_project_proposals_id');
    }
}
