<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;

    protected $table = 'forms';
    protected $fillable = [
        'event_id',
        'name',
        'code',
        'file',
        'is_dean',
        'is_asd',
        'is_ad'
    ];
    protected $casts = [
        'is_dean' => 'boolean',
        'is_asd' => 'boolean',
        'is_ad' => 'boolean'
    ];

    public function events() {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
