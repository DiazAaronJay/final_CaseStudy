<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::all();
        return response()->json($doctors);
    }

    public function store(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string',
            'email' => 'required|string|email|max:50|unique:users',
            'specialization' => 'nullable|string',
        ]);

        $password = 'doctor@123';

        $user = User::create([
            'name' => $request->fullname,
            'email' => $request->email,
            'password' => $password,
            'role' => 'doctor',
        ]);

        $user_id = $user->id;

        $doctor = Doctor::create([
            'user_id'=>$user_id,
            'fullname' => $request->fullname,
            'email' => $request->email,
            'specialization' => $request->specialization,
        ]);

        $doctor->save();

        return response()->json($doctor, 201);
    }

    public function show($id)
    {
        $doctor = Doctor::findOrFail($id);
        return response()->json($doctor);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'fullname' => 'required|string',
            'email' => 'nullable|string',
            'specialization' => 'nullable|string',
        ]);

        $doctor = Doctor::findOrFail($id);
        $doctor->update($request->all());

        return response()->json($doctor, 200);
    }

    public function destroy($id)
    {
        $doctor = Doctor::findOrFail($id);
        $doctor->delete();

        return response()->json(null, 204);
    }
}
