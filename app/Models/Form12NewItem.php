<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form12NewItem extends Model
{
    use HasFactory;

    protected $table = 'form12_new_items';

    protected $fillable = [
        'form12_id',
        'topic',
        'discussion',
        'resolution',
    ];

    public function form12()
    {
        return $this->belongsTo(Form12::class, 'form12_id');
    }
}
