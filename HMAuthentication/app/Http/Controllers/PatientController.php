<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::all();
        return response()->json($patients);
    }

    public function store(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string',
            'email' => 'required|string|email|max:50|unique:users',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $password = 'patient@123';

        $user = User::create([
            'name' => $request->fullname,
            'email' => $request->email,
            'password' => $password,
            'role' => 'patient',
        ]);

        $user_id = $user->id;

        $patient = Patient::create([
            'user_id'=>$user_id,
            'fullname' => $request->fullname,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        $patient->save();

        return response()->json($patient, 201);
    }

    public function show($id)
    {
        $patient = Patient::findOrFail($id);
        return response()->json($patient);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'fullname' => 'required|string',
            'email' => 'required|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $patient = Patient::findOrFail($id);
        $patient->update($request->all());

        return response()->json($patient, 200);
    }

    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        $patient->delete();

        return response()->json(null, 204);
    }
}
