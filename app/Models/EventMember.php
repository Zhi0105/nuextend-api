<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventMember extends Model
{
    use HasFactory, SoftDeletes;


    protected $table = 'event_members';
    protected $fillable = [
        'event_id',
        'user_id',
        'role'
    ];

    protected $dates = ['deleted_at'];

    public function Event() {
        return $this->belongsTo(Event::class, 'event_id');
    }
    public function user() {
        return $this->belongsTo(User::class, 'user_id');

    }
}
