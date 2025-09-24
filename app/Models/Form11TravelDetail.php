<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Form11TravelDetail extends Model
{
    use HasFactory;

    protected $table = 'form11_travel_details';

    protected $fillable = [
        'form11_id',
        'date',
        'from',
        'to',
        'departure',
        'arrival',
        'trip_duration',
        'purpose',
    ];

    protected $casts = [
        'date' => 'date',
        'departure' => 'datetime',
        'arrival' => 'datetime',
    ];

    public function form11()
    {
        return $this->belongsTo(Form11::class, 'form11_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($travelDetail) {
            if ($travelDetail->departure && $travelDetail->arrival) {
                $departure = Carbon::parse($travelDetail->departure);
                $arrival = Carbon::parse($travelDetail->arrival);

                $diff = $departure->diff($arrival);

                $travelDetail->trip_duration = $diff->format('%h hours %i minutes');
            }
        });
    }
}
