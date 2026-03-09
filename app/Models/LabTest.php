<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabTest extends Model
{
    //
    protected $fillable = [
        'name',
        'code',
        'category_id',
        'name',
        'method_id',
        'sample_id',
        'reference_from',
        'reference_to',
        'unit',
        'price',
        'result_type',
        'testable',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(LabCategory::class, 'category_id');
    }

    public function method()
    {
        return $this->belongsTo(LabMethod::class, 'method_id');
    }

    public function sample()
    {
        return $this->belongsTo(LabSample::class, 'sample_id');
    }

    public function services()
    {
        return $this->morphMany(Service::class, 'service');
    }

    public function labResults()
    {
        return $this->hasMany(LabResult::class);
    }
}
