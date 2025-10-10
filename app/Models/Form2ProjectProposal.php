<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form2ProjectProposal extends Model
{
    use HasFactory;
    protected $table = 'form2_project_proposals';
    protected $fillable = [
        'event_id',
        'event_type_id',
        'proponents',
        'collaborators',
        'participants',
        'partners',
        'implementationDate',
        'area',
        'budgetRequirement',
        'budgetRequested',
        'background',
        'otherInfo',
        'is_commex',
        'is_dean',
        'is_asd',
        'is_ad',
        'commex_remarks',
        'dean_remarks',
        'asd_remarks',
        'ad_remarks',
        'is_updated',
        'is_revised',
        'commex_approved_by',
        'dean_approved_by',
        'asd_approved_by',
        'ad_approved_by',
        'commex_approve_date',
        'dean_approve_date',
        'asd_approve_date',
        'ad_approve_date'
        
    ];

    // If you’ll store a real date in implementationDate, you can cast:
    // protected $casts = ['implementationDate' => 'date'];



    public function event() {
        return $this->belongsTo(Event::class, 'event_id');
    }
    // Relationships
    public function eventType() {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }
    public function objectives() {
        return $this->hasMany(Form2ProjectObjective::class, 'form2_project_proposals_id');
    }
    public function impactOutcomes() {
        return $this->hasMany(Form2ProjectImpactOutcome::class, 'form2_project_proposals_id');
    }
    public function risks() {
        return $this->hasMany(Form2ProjectRisk::class, 'form2_project_proposals_id');
    }
    public function staffings() {
        return $this->hasMany(Form2ProjectStaffing::class, 'form2_project_proposals_id');
    }
    public function workPlans() {
        return $this->hasMany(Form2ProjectWorkPlan::class, 'form2_project_proposals_id');
    }
    public function detailedBudgets() {
        return $this->hasMany(Form2ProjectDetailedBudget::class, 'form2_project_proposals_id');
    }

    public function commexApprover() {
        return $this->belongsTo(User::class, 'commex_approved_by')->withDefault();
    }
    public function deanApprover() {
        return $this->belongsTo(User::class, 'dean_approved_by')->withDefault();
    }
    public function asdApprover() {
        return $this->belongsTo(User::class, 'asd_approved_by')->withDefault();
    }
    public function adApprover() {
        return $this->belongsTo(User::class, 'ad_approved_by')->withDefault();
    }
}
