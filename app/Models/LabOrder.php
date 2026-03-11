<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabOrder extends Model
{
    protected $fillable = [
        'appointment_id',
        'service_type',
        'service_id',
        'sample_id',
        'status',
        'collected_at',
        'collected_by',
        'collection_notes',
        'results',
        'technician',
        'remarks',
        'completed_at',
        'dispatch_method',
        'dispatched_at',
        'received_by',
        'dispatch_notes',
    ];

    protected $casts = [
        'collected_at' => 'datetime',
        'completed_at' => 'datetime',
        'dispatched_at' => 'datetime',
        'results' => 'array',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Get the related service (LabTest or LabGroup)
     */
    public function service()
    {
        return $this->morphTo('service', 'service_type', 'service_id');
    }

    public function labResults()
    {
        return $this->hasMany(LabResult::class);
    }
}
