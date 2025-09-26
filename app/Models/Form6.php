<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form6 extends Model
{
    use HasFactory;

    protected $table = 'form6';

    protected $fillable = [
        'event_id',
        'designation',
        'representing',
        'partnership',
        'entitled',
        'conducted_on',
        'behalf_of',
        'organization',
        'address',
        'mobile_number',
        'email',

        'is_commex', 'is_dean', 'is_asd', 'is_ad',
        'commex_remarks', 'dean_remarks', 'asd_remarks', 'ad_remarks',

        'commex_approved_by', 'dean_approved_by', 'asd_approved_by', 'ad_approved_by',
        'commex_approve_date', 'dean_approve_date', 'asd_approve_date', 'ad_approve_date',
    ];

    protected $casts = [
        'is_commex' => 'boolean',
        'is_dean' => 'boolean',
        'is_asd' => 'boolean',
        'is_ad' => 'boolean',

        'commex_approve_date' => 'date',
        'dean_approve_date' => 'date',
        'asd_approve_date' => 'date',
        'ad_approve_date' => 'date',
    ];

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
