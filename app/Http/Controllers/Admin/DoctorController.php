<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Doctor;
use App\Support\HandlesImageUpload;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    use HandlesImageUpload;

    public function index()
    {
        $doctors = Doctor::query()->with('mainBranch')->latest()->paginate(15);

        return view('admin.doctors.index', compact('doctors'));
    }

    public function create()
    {
        $branches = Branch::query()->orderBy('sort_order')->get();

        return view('admin.doctors.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'specialty_ar' => ['required', 'string', 'max:255'],
            'specialty_en' => ['required', 'string', 'max:255'],
            'years_experience' => ['required', 'integer', 'min:0'],
            'bio_ar' => ['nullable', 'string'],
            'bio_en' => ['nullable', 'string'],
            'expertise_ar' => ['nullable', 'string'],
            'expertise_en' => ['nullable', 'string'],
            'booking_method_ar' => ['nullable', 'string'],
            'booking_method_en' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'branch_ids' => ['nullable', 'array'],
            'branch_ids.*' => ['exists:branches,id'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['photo'] = $this->storeImage($request->file('photo'), 'doctors');
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $doctor = Doctor::create($data);
        $doctor->branches()->sync($request->input('branch_ids', []));

        return redirect()->route('admin.doctors.index', app()->getLocale())->with('success', 'Saved successfully');
    }

    public function show($doctor)
    {
        $doctor = $this->resolveDoctor($doctor);

        return view('admin.doctors.show', compact('doctor'));
    }

    public function edit($doctor)
    {
        $doctor = $this->resolveDoctor($doctor);
        $branches = Branch::query()->orderBy('sort_order')->get();

        return view('admin.doctors.edit', compact('doctor', 'branches'));
    }

    public function update(Request $request, $doctor)
    {
        $doctor = $this->resolveDoctor($doctor);

        $data = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'specialty_ar' => ['required', 'string', 'max:255'],
            'specialty_en' => ['required', 'string', 'max:255'],
            'years_experience' => ['required', 'integer', 'min:0'],
            'bio_ar' => ['nullable', 'string'],
            'bio_en' => ['nullable', 'string'],
            'expertise_ar' => ['nullable', 'string'],
            'expertise_en' => ['nullable', 'string'],
            'booking_method_ar' => ['nullable', 'string'],
            'booking_method_en' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'branch_ids' => ['nullable', 'array'],
            'branch_ids.*' => ['exists:branches,id'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['photo'] = $this->storeImage($request->file('photo'), 'doctors', $doctor->photo);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $doctor->update($data);
        $doctor->branches()->sync($request->input('branch_ids', []));

        return redirect()->route('admin.doctors.index', app()->getLocale())->with('success', 'Updated successfully');
    }

    public function destroy($doctor)
    {
        $doctor = $this->resolveDoctor($doctor);
        $doctor->delete();

        return redirect()->route('admin.doctors.index', app()->getLocale())->with('success', 'Deleted successfully');
    }

    private function resolveDoctor(mixed $doctor): Doctor
    {
        if ($doctor instanceof Doctor) {
            return $doctor;
        }

        return Doctor::query()->findOrFail((int) $doctor);
    }
}
