<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;

class AppointmentController extends Controller
{
    use AuthorizesRequests;

    public function index()
{
    $user = Auth::user();
    $role = $user->role;

    if ($role === 'admin') {
        $appointments = Appointment::with(['patient', 'doctor'])->get();
    } elseif ($role === 'doctor') {
        $appointments = Appointment::with('patient')->where('doctor_id', $user->id)->get();
    } else {
        $appointments = Appointment::with('doctor')->where('patient_id', $user->id)->get();
    }

    return response()->json($appointments);
}

    public function store(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'doctor_id' => [
                'required',
                Rule::exists('doctors', 'user_id')->where(function ($query) use ($request) {
                    $query->where('user_id', $request->doctor_id);
                }),
            ],
            'appointment_date' => 'required|date',
            'reason' => 'required|string',
        ]);

        $appointment = Appointment::create([
            'patient_id' => $user->id,
            'doctor_id' => $validatedData['doctor_id'],
            'appointment_date' => $validatedData['appointment_date'],
            'status' => 'scheduled',
            'reason' => $validatedData['reason'],
        ]);

        return response()->json($appointment, 201);
    }

    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
    
        $appointment->update($request->only('status'));
    
        LOG::info($appointment);
        if ($request->status === 'completed') {
            try {
                MedicalRecord::create([
                    'patient_id' => $request->patient_id, 
                    'doctor' => $request->doctor_name, 
                    'description' => $request->description, 
                ]);
            } catch (\Exception $e) {
                Log::error('Error creating medical record: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to create medical record'], 500);
            }
        }
    
        return response()->json($appointment);
    }
    

    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);

        $appointment->delete();

        return response()->json(null, 204);
    }
}
