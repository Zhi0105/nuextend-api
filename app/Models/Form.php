<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;

    protected $table = 'forms';
    protected $fillable = [
        'event_id',
        'name',
        'code',
        'file',
        'is_commex',
        'is_dean',
        'is_asd',
        'is_ad',
        'commex_remarks',
        'dean_remarks',
        'asd_remarks',
        'ad_remarks',
        'commex_approved_by',
        'dean_approved_by',
        'asd_approved_by',
        'ad_approved_by',
        'commex_approve_date',
        'dean_approve_date',
        'asd_approve_date',
        'ad_approve_date'
    ];
    protected $casts = [
        'is_dean' => 'boolean',
        'is_asd' => 'boolean',
        'is_ad' => 'boolean'
    ];

    public function events() {
        return $this->belongsToMany(Event::class, 'event_form')->withTimestamps();
    }
    public function commexApprover() {
        return $this->belongsTo(User::class, 'commex_approved_by');
    }
    public function deanApprover() {
        return $this->belongsTo(User::class, 'dean_approved_by');
    }
    public function asdApprover() {
        return $this->belongsTo(User::class, 'asd_approved_by');
    }
    public function adApprover() {
        return $this->belongsTo(User::class, 'ad_approved_by');
    }
}
