<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';
    protected $fillable = ['name'];

    public function user() {
        return $this->hasOne(User::class, 'role_id');
    }

    public function eventmember() {
        return $this->hasMany(EventMember::class, 'role_id');
    }
}
