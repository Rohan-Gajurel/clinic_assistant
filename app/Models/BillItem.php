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
}
