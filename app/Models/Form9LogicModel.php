<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form9LogicModel extends Model
{
    use HasFactory;

    protected $table = 'form9_logic_models';

    protected $fillable = [
        'form9_id',
        'objectives',
        'inputs',
        'activities',
        'outputs',
        'outcomes',
    ];

    // Belongs to a Form9
    public function form9()
    {
        return $this->belongsTo(Form9::class, 'form9_id');
    }
    
}
