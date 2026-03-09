<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    //
    protected $fillable = ['service_id', 'service_type','lab_order_id'];

    public function labOrder()
    {
        return $this->belongsTo(LabOrder::class);
    }

    public function service_()
    {
        return $this->morphTo();
    }

    public function labGroup()
    {
        return $this->belongsTo(LabGroup::class, 'service_id', 'id');
    }


}
