<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form2ProjectStaffing extends Model
{
    use HasFactory;
    protected $table = 'form2_project_staffings';
    protected $fillable = [
        'form2_project_proposals_id',
        'staff',
        'responsibilities',
        'contact',
    ];

    public function proposal() {
        return $this->belongsTo(Form2ProjectProposal::class, 'form2_project_proposals_id');
    }
}
