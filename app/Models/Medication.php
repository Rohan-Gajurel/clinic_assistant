<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    protected $fillable = [
        'appointment_id',
        'drug_name',
        'route',
        'dose',
        'dose_unit',
        'frequency',
        'duration_value',
        'duration_unit',
        'instructions',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get formatted frequency
     */
    public function getFormattedFrequencyAttribute()
    {
        $frequencies = [
            'once_daily' => 'Once a day',
            'twice_daily' => 'Twice a day',
            'thrice_daily' => 'Thrice a day',
            'four_times_daily' => 'Four times a day',
            'as_needed' => 'As needed',
            'every_4_hours' => 'Every 4 hours',
            'every_6_hours' => 'Every 6 hours',
            'every_8_hours' => 'Every 8 hours',
            'every_12_hours' => 'Every 12 hours',
            'weekly' => 'Weekly',
        ];

        return $frequencies[$this->frequency] ?? $this->frequency;
    }
}
