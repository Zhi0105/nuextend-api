<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form1BudgetSummary extends Model
{
    use HasFactory;
    protected $table = 'form1_project_budget_summary';
    protected $fillable = [
    'form1_projects_id',
    'activities',
    'outputs',
    'timeline',
    'personnel',
    'budget'
    ];

    public function project() {
        return $this->belongsTo(Form1Project::class, 'form1_projects_id');
    }
}
