<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
    use HasFactory;

    protected $table = 'event_types';
    protected $fillable = ['name'];

    public function events() {
        return $this->hasMany(Event::class, 'event_type_id');
    }
    public function ProjectProposals() {
        return $this->hasMany(ProjectProposal::class, 'event_type_id');
    }
    public function ProjectProposals2() {
        return $this->hasMany(Form2ProjectProposal::class, 'event_type_id');
    }

}
