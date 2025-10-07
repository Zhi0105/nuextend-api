<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormRemark extends Model
{
    use HasFactory;

    protected $table = 'form_remarks';

    protected $fillable = [
        'form_type',
        'form_id',
        'event_id',
        'user_id',
        'remark',
    ];

    /**
     * Get the user who made the remark.
     */
    public function user(){
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    public function event() {
        return $this->belongsTo(Event::class, 'event_id')->withDefault();
    }
}
