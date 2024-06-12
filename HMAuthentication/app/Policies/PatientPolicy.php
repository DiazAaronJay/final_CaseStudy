<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PatientPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Patient $patient = null): bool
    {
        if (!$patient) {
            return false; 
        }
    
        return $user->role === 'admin' || $user->id === $patient->user_id;
    }
    


    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Patient $patient): bool
    {
        return $user->role === 'admin' || $user->id === $patient->user_id;
    }

    public function delete(User $user, Patient $patient): bool
    {
        return $user->role === 'admin';
    }
}
