<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form10Oaopb extends Model
{
    use HasFactory;

    protected $table = 'form10_oaopb';

    protected $fillable = [
        'form10_id',
        'objectives',
        'activities',
        'outputs',
        'personnel',
        'budget',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
    ];

    public function form10()
    {
        return $this->belongsTo(Form10::class, 'form10_id');
    }
}
