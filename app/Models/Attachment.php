<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    // Explicitly define the table since it's not the plural "attachments"
    protected $table = 'attachment';

    // Mass assignable attributes
    protected $fillable = [
        'event_id',
        'name',
        'file',
        'remarks',
    ];

    public function event(){
        return $this->belongsTo(Event::class, 'event_id');
    }
}
