<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabGroup extends Model
{

    protected $fillable = [
        'name',
        'charge_amount',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(LabCategory::class, 'category_id');
    }

    public function tests()
    {
        return $this->belongsToMany(LabTest::class, 'lab_group_test', 'group_id', 'test_id');
    }

    public function labOrders(){
        return $this->hasMany(LabOrder::class, 'service');
    }
}
