<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = [
        'patient_id',
        'appointment_id',
        'gross_amount',
        'discount_amount',
        'net_amount',
        'status',
        'cancellation_reason',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function items()
    {
        return $this->hasMany(BillItem::class);
    }

    /**
     * Get items with dispatched lab results
     */
    public function dispatchedItems()
    {
        return $this->hasMany(BillItem::class)->where('sample_status', 'dispatched');
    }
}
