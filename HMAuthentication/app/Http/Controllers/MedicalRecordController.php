<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MedicalRecordController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            $records = MedicalRecord::with('patient')->get();
        } elseif ($user->role === 'doctor') {
            $records = MedicalRecord::with('patient')->where('doctor', $user->name)->get();
                
        } elseif ($user->role === 'patient') {
            $records = MedicalRecord::where('patient_id', $user->id)->get();
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        foreach ($records as $record) {
            if (!$record->patient) {
                $patient = \App\Models\User::find($record->patient_id);
                $record->patient_name = $patient ? $patient->name : 'Unknown';
            } else {
                $record->patient_name = $record->patient->name;
            }
        
        }

        return response()->json($records);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $record = MedicalRecord::findOrFail($id);

        if ($user->role !== 'doctor' || $record->doctor !== $user->name) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $record->update($request->only('description'));
        return response()->json($record);
    }
}
