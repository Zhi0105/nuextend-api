<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory, SoftDeletes;


    protected $table = 'organizations';
    protected $fillable = ['name'];


    protected $dates = ['deleted_at'];

    public function users() {
        return $this->belongsToMany(User::class, 'organization_members')
                ->withPivot('role_id', 'created_at', 'updated_at')
                ->withTimestamps();
    }
    public function events() {
        return $this->hasMany(Event::class, 'organization_id');
    }
}
