<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Targetgroup extends Model
{
    use HasFactory;
    protected $table = 'target_groups';
    protected $fillable = [
        'name'
    ];

    public function event() {
        return $this->hasMany(Event::class, 'target_group_id');
    }
}
