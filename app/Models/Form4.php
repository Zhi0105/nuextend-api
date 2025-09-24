<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form4 extends Model
{
    use HasFactory;
    // Explicit table name (since it's not plural "form4_migrations")
    protected $table = 'form4';

    // Allow mass assignment for these fields
    protected $fillable = [
        'event_id',
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
        'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p',

        'is_commex', 'is_dean', 'is_asd', 'is_ad',
        'commex_remarks', 'dean_remarks', 'asd_remarks', 'ad_remarks',

        'commex_approved_by', 'dean_approved_by', 'asd_approved_by', 'ad_approved_by',
        'commex_approve_date', 'dean_approve_date', 'asd_approve_date', 'ad_approve_date',
    ];

    // Cast boolean and date fields
    protected $casts = [
        'a' => 'boolean',
        'b' => 'boolean',
        'c' => 'boolean',
        'd' => 'boolean',
        'e' => 'boolean',
        'f' => 'boolean',
        'g' => 'boolean',
        'h' => 'boolean',
        'i' => 'boolean',
        'j' => 'boolean',
        'k' => 'boolean',
        'l' => 'boolean',
        'm' => 'boolean',
        'n' => 'boolean',
        'o' => 'boolean',
        'p' => 'boolean',

        'is_commex' => 'boolean',
        'is_dean' => 'boolean',
        'is_asd' => 'boolean',
        'is_ad' => 'boolean',

        'commex_approve_date' => 'date',
        'dean_approve_date' => 'date',
        'asd_approve_date' => 'date',
        'ad_approve_date' => 'date',
    ];

    // Relationships (assuming you have a User model)
     public function event() {
        return $this->belongsTo(Event::class, 'event_id');
    }
    
    public function commexApprover()
    {
        return $this->belongsTo(User::class, 'commex_approved_by');
    }

    public function deanApprover()
    {
        return $this->belongsTo(User::class, 'dean_approved_by');
    }

    public function asdApprover()
    {
        return $this->belongsTo(User::class, 'asd_approved_by');
    }

    public function adApprover()
    {
        return $this->belongsTo(User::class, 'ad_approved_by');
    }
}
