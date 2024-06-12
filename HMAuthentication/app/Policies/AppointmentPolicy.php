<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppointmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('doctor') || $user->hasRole('patient');
    }

    public function view(User $user, Appointment $appointment): bool
    {
        return $user->hasRole('admin') ||
               ($user->hasRole('doctor') && $user->id === $appointment->doctor_id) ||
               ($user->hasRole('patient') && $user->id === $appointment->patient_id);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('patient');
    }

    public function update(User $user, Appointment $appointment): bool
    {
        return $user->hasRole('doctor') && $user->id === $appointment->doctor_id;
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return $user->hasRole('patient') && $user->id === $appointment->patient_id;
    }
}
