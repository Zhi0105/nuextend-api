<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form1ComponentProject extends Model
{
    use HasFactory;
    protected $table = 'form1_component_projects';
    protected $fillable = [
    'form1_program_proposals_id',
    'title',
    'outcomes',
    'budget' // change to decimal if you change the migration
    ];


    public function proposal() {
        return $this->belongsTo(Form1ProgramProposal::class, 'form1_program_proposals_id');
    }
}
