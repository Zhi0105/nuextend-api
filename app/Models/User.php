<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\CustomVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'department_id',
        'program_id',
        'role_id',
        'school_id',
        'firstname',
        'middlename',
        'lastname',
        'email',
        'email_verified_at',
        'password',
        'contact',
        'status',
        'is_EsVolunteer',
        'is_officer',
        'esign'
    ];

    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the esign attribute with proper base64 encoding
     */
    public function getEsignAttribute($value)
    {
        if (!$value) {
            return null;
        }

        // If it's already a base64 string, return as is
        if (is_string($value) && strpos($value, 'data:image') === 0) {
            return $value;
        }

        // Handle binary data from database
        $binaryData = $value;
        
        // If it's a resource (BLOB), get the contents
        if (is_resource($value)) {
            $binaryData = stream_get_contents($value);
        }

        if ($binaryData && !empty($binaryData)) {
            // Detect MIME type more reliably
            $mimeType = $this->detectImageType($binaryData);
            $base64 = base64_encode($binaryData);
            
            return "data:{$mimeType};base64,{$base64}";
        }

        return null;
    }

    /**
     * Set the esign attribute - handle base64 image data
     */
    public function setEsignAttribute($value)
    {
        // If it's a base64 image string from frontend
        if (is_string($value) && strpos($value, 'data:image') === 0) {
            $parts = explode(',', $value, 2);
            if (count($parts) === 2) {
                $base64Data = $parts[1];
                $imageData = base64_decode($base64Data);
                
                // Validate it's a proper image
                if ($imageData !== false && $this->isValidImageData($imageData)) {
                    $this->attributes['esign'] = $imageData;
                    return;
                }
            }
        }
        
        // Set null if empty or invalid, otherwise set the value directly
        $this->attributes['esign'] = empty($value) ? null : $value;
    }

    /**
     * Detect image type from binary data
     */
    private function detectImageType($data)
    {
        // Check for JPEG
        if (str_starts_with($data, "\xFF\xD8\xFF")) {
            return 'image/jpeg';
        }
        
        // Check for PNG
        if (str_starts_with($data, "\x89PNG\r\n\x1a\n")) {
            return 'image/png';
        }
        
        // Check for GIF
        if (str_starts_with($data, "GIF8")) {
            return 'image/gif';
        }
        
        // Check for WebP
        if (str_starts_with($data, "RIFF") && substr($data, 8, 4) === "WEBP") {
            return 'image/webp';
        }

        // Default to JPEG if unknown
        return 'image/jpeg';
    }

    /**
     * Validate if binary data is a valid image
     */
    private function isValidImageData($data)
    {
        if (empty($data)) {
            return false;
        }

        return (
            str_starts_with($data, "\xFF\xD8\xFF") || // JPEG
            str_starts_with($data, "\x89PNG\r\n\x1a\n") || // PNG
            str_starts_with($data, "GIF8") || // GIF
            (str_starts_with($data, "RIFF") && substr($data, 8, 4) === "WEBP") // WebP
        );
    }

    public function department() {
        return $this->belongsTo(Department::class, 'department_id');
    }
    public function program() {
        return $this->belongsTo(Program::class, 'program_id');
    }
    public function role() {
        return $this->belongsTo(Role::class, 'role_id');
    }
    public function skill() {
        return $this->belongsToMany(Skill::class, 'user_skills')->withTimestamps();
    }
    public function organizations() {
        return $this->belongsToMany(Organization::class, 'organization_members')
                    ->withPivot('role_id', 'created_at', 'updated_at')
                    ->withTimestamps();
    }
    public function events() {
        return $this->hasMany(Event::class, 'user_id');
    }
    public function participants() {
        return $this->hasMany(Participant::class, 'user_id');
    }
    public function commexApprovedForms() {
        return $this->hasOne(Form::class, 'commex_approved_by');
    }
    public function deanApprovedForms() {
        return $this->hasMany(Form::class, 'dean_approved_by');
    }
    public function asdApprovedForms() {
        return $this->hasMany(Form::class, 'asd_approved_by');
    }
    public function adApprovedForms() {
        return $this->hasMany(Form::class, 'ad_approved_by');
    }
    public function sendEmailVerificationNotification() {
        $this->notify(new CustomVerifyEmail());
    }


    // FORM1
    public function form1ProposalsCommexApproved() {
        return $this->hasMany(Form1ProgramProposal::class, 'commex_approved_by');
    }
    public function form1ProposalsDeanApproved() {
        return $this->hasMany(Form1ProgramProposal::class, 'dean_approved_by');
    }
    public function form1ProposalsAsdApproved() {
        return $this->hasMany(Form1ProgramProposal::class, 'asd_approved_by');
    }
    public function form1ProposalsAdApproved() {
        return $this->hasMany(Form1ProgramProposal::class, 'ad_approved_by');
    }

    // FORM2
    public function form2ProposalsCommexApproved() {
        return $this->hasMany(Form2ProjectProposal::class, 'commex_approved_by');
    }
    public function form2ProposalsDeanApproved() {
        return $this->hasMany(Form2ProjectProposal::class, 'dean_approved_by');
    }
    public function form2ProposalsAsdApproved() {
        return $this->hasMany(Form2ProjectProposal::class, 'asd_approved_by');
    }
    public function form2ProposalsAdApproved() {
        return $this->hasMany(Form2ProjectProposal::class, 'ad_approved_by');
    }

    // FORM3
    public function form3ProposalsCommexApproved() {
        return $this->hasMany(Form3OutreachProposal::class, 'commex_approved_by');
    }
    public function form3ProposalsDeanApproved() {
        return $this->hasMany(Form3OutreachProposal::class, 'dean_approved_by');
    }
    public function form3ProposalsAsdApproved() {
        return $this->hasMany(Form3OutreachProposal::class, 'asd_approved_by');
    }
    public function form3ProposalsAdApproved() {
        return $this->hasMany(Form3OutreachProposal::class, 'ad_approved_by');
    }

    public function formremarksuser() {
        return $this->hasMany(FormRemark::class, 'user_id');
    }

    public function eventmember() {
        return $this->hasMany(Eventmember::class, 'user_id');
    }
}
