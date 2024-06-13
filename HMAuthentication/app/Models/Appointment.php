<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    // this fills the patient doctor as appointment with status and reason
    protected $fillable = [
        'patient_id', 'doctor_id', 'appointment_date', 'status', 'reason'
    ];

    // this function for patient that this appointment belongs to patient
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    // this function for patient that this appointment belongs to doctors
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
