<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form1BudgetSummary extends Model
{
    use HasFactory;
    protected $table = 'form1_budget_summary';
    protected $fillable = [
    'form1_program_proposals_id',
    'activities',
    'outputs',
    'timeline',
    'personnel'
    ];


    public function proposal() {
        return $this->belongsTo(Form1ProgramProposal::class, 'form1_program_proposals_id');
    }
}
