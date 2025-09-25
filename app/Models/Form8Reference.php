<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form8Reference extends Model
{
    use HasFactory;

    protected $table = 'form8_references';

    protected $fillable = [
        'form8_id',
        'reference',
    ];

    // Belongs to a Form8
    public function form8()
    {
        return $this->belongsTo(Form8::class, 'form8_id');
    }
}
