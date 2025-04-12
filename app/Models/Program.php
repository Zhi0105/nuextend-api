<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'programs';
    protected $fillable = ['department_id', 'name'];

    protected $dates = ['deleted_at'];

    public function department() {
        return $this->belongsTo(Department::class, 'department_id');
    }
    public function user() {
        return $this->hasOne(User::class, 'program_id');
    }

}
