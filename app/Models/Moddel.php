<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Moddel extends Model
{
    use HasFactory;

    protected $table = 'models';
    protected $fillable = ['name'];

    public function events() {
        return $this->hasMany(Event::class, 'model_id');
    }
}
