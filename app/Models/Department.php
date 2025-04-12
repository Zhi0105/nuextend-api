<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'departments';
    protected $fillable = ['name'];

    protected $dates = ['deleted_at'];

    public function user() {
        return $this->hasMany(User::class, 'department_id');
    }
    public function program() {
        return $this->hasMany(Program::class, 'department_id');
    }
}


