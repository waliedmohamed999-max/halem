<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Doctor;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::query()->with('mainBranch')->where('is_active', true)->orderBy('sort_order')->paginate(12);

        return view('front.doctors.index', compact('doctors'));
    }

    public function show(int $id)
    {
        $doctor = Doctor::query()->with(['mainBranch', 'branches'])->where('is_active', true)->findOrFail($id);

        return view('front.doctors.show', compact('doctor'));
    }
}
