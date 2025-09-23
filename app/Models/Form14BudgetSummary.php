<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form14BudgetSummary extends Model
{
    use HasFactory;

    protected $table = 'budget_summaries';

    protected $fillable = [
        'form14_id',
        'cost',
        'item',
        'personnel',
        'quantity',
        'description',
    ];

    public function form14()
    {
        return $this->belongsTo(Form14::class, 'form14_id', 'form14_id');
    }
}
