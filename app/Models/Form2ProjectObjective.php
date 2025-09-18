<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form2ProjectObjective extends Model
{
    use HasFactory;
    protected $table = 'form2_project_objectives';

    protected $fillable = [
        'form2_project_proposals_id',
        'objectives',
        'strategies',
    ];

    public function proposal() {
        return $this->belongsTo(Form2ProjectProposal::class, 'form2_project_proposals_id');
    }
}
