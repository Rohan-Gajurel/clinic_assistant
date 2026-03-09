<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabResult extends Model
{
    protected $fillable = [
        'lab_order_id',
        'lab_test_id',
        'service_id',
        'numeric_value',
        'text_value',
        'reference_from',
        'reference_to',
        'unit',
        'status',
        'remarks',
        'entered_by',
        'entered_at',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'numeric_value' => 'decimal:4',
        'reference_from' => 'decimal:2',
        'reference_to' => 'decimal:2',
        'entered_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the lab order that owns this result
     */
    public function labOrder()
    {
        return $this->belongsTo(LabOrder::class);
    }

    /**
     * Get the lab test for this result
     */
    public function labTest()
    {
        return $this->belongsTo(LabTest::class);
    }

    /**
     * Get the service for this result
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the result value based on test result_type
     */
    public function getValueAttribute()
    {
        if ($this->labTest && $this->labTest->result_type === 'text') {
            return $this->text_value;
        }
        return $this->numeric_value;
    }

    /**
     * Get formatted reference range
     */
    public function getReferenceRangeAttribute()
    {
        if ($this->reference_from && $this->reference_to) {
            return $this->reference_from . ' - ' . $this->reference_to . ' ' . ($this->unit ?? '');
        }
        return null;
    }

    /**
     * Check if result is within normal range
     */
    public function isNormal()
    {
        if ($this->labTest && $this->labTest->result_type === 'numeric' && $this->numeric_value) {
            return $this->numeric_value >= $this->reference_from && $this->numeric_value <= $this->reference_to;
        }
        return $this->status === 'normal';
    }
}
