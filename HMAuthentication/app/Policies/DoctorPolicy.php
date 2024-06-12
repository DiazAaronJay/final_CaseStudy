<?php

namespace App\Policies;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DoctorPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Doctor $doctor = null): bool
    {
        if (!$doctor) {
            return false; 
        }
    
        return $user->role === 'admin' || $user->id === $doctor->user_id;
    }
    


    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Doctor $doctor): bool
    {
        return $user->role === 'admin' || $user->id === $doctor->user_id;
    }

    public function delete(User $user, Doctor $doctor): bool
    {
        return $user->role === 'admin';
    }
}
