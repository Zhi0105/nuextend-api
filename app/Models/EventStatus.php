<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventStatus extends Model
{
    use HasFactory;

    protected $table = 'event_status';
    protected $fillable = ['name'];

    public function events() {
        return $this->hasMany(Event::class, 'event_status_id');
    }
}
