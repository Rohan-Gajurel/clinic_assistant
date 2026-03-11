<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'full_name',
        'age',
        'age_unit',
        'date_of_birth',
        'sex',
        'marital_status',
        'contact_number',
        'email',
        'address',
        'blood_group',
        'id_card_type',
        'id_card_number',
        'nationality',
        'patient_type',
        'province',
        'district',
        'local_level',
        'ward_number',
        'photo',
    ];

    
    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function observationVitals()
    {
        return $this->hasOne(ObservationVital::class);
    }

    public function diseaseHistory()
    {
        return $this->hasMany(DiseaseHistory::class);
    }

    public function drugHistory()
    {
        return $this->hasMany(DrugHistory::class);
    }

    public function diagnoses()
    {
        return $this->hasMany(Diagnosis::class);
    }

    public function medications()
    {
        return $this->hasMany(Medication::class);
    }
}
