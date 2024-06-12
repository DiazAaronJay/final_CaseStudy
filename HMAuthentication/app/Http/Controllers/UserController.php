<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        switch ($user->role) {
            case 'admin':
                $admin = User::find($user->id);
                break;
            case 'doctor':
                $doctors = Doctor::with('user')->where('user_id', $user->id)->first();
                break;
            case 'patient':
                $patients = Patient::with('user')->where('user_id', $user->id)->first();
                LOG::info($patients);
                break;
            default:
                return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json(['patients' => $patients ?? null, 'doctors' => $doctors ?? null, 'admin' => $admin ?? null]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        
        $userRecord = User::find($user->id);
        $userRecord->update($request->only(['name', 'email']));
        
        switch ($user->role) {
            case 'doctor':
                $doctor = Doctor::where('user_id', $user->id)->first();
                if ($doctor) {
                    $doctor->fullname = $request->input('name');
                    $doctor->email = $request->input('email');
                    $doctor->specialization = $request->input('specialization');
                    $doctor->save();
                }
                break;
            case 'patient':
                $patient = Patient::where('user_id', $user->id)->first();
                if ($patient) {
                    $patient->fullname = $request->input('name');
                    $patient->email = $request->input('email');
                    $patient->phone = $request->input('phone');
                    $patient->address = $request->input('address');
                    $patient->save();
                }
                break;
            default:
                break;
        }

        return response()->json(['message' => 'User profile updated successfully']);
    }

    
}