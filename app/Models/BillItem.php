<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillItem extends Model
{
    protected $fillable = [
        'bill_id',
        'service_type',
        'service_id',
        'service_name',
        'quantity',
        'rate',
        'amount',
        'discount',
        'net_amount',
        'sample_status',
        'sample_id',
        'collected_at',
        'collected_by',
        'collection_notes',
    ];

    protected $casts = [
        'collected_at' => 'datetime',
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    /**
     * Get the related service (LabTest, LabGroup, etc.)
     */
    public function service()
    {
        return $this->morphTo('service', 'service_type', 'service_id');
    }

    /**
     * Get all lab results for this bill item
     */
    public function labResults()
    {
        return $this->hasMany(LabResult::class);
    }

    /**
     * Check if this item is a LabGroup
     */
    public function isLabGroup()
    {
        return $this->service_type === 'App\\Models\\LabGroup';
    }

    /**
     * Check if this item is a LabTest
     */
    public function isLabTest()
    {
        return $this->service_type === 'App\\Models\\LabTest';
    }

    /**
     * Get all tests for this item (single test or group tests)
     */
    public function getTests()
    {
        if ($this->isLabGroup() && $this->service) {
            return $this->service->tests;
        } elseif ($this->isLabTest() && $this->service) {
            return collect([$this->service]);
        }
        return collect();
    }
}
