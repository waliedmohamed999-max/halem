<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CareerPosition;
use Illuminate\Http\Request;

class CareerPositionController extends Controller
{
    public function index()
    {
        $positions = CareerPosition::query()->latest()->paginate(15);

        return view('admin.career-positions.index', compact('positions'));
    }

    public function create()
    {
        return view('admin.career-positions.create');
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        CareerPosition::query()->create($data);

        return redirect()->route('admin.career-positions.index')->with('success', 'Saved successfully');
    }

    public function show(CareerPosition $careerPosition)
    {
        return view('admin.career-positions.show', compact('careerPosition'));
    }

    public function edit(CareerPosition $careerPosition)
    {
        return view('admin.career-positions.edit', compact('careerPosition'));
    }

    public function update(Request $request, CareerPosition $careerPosition)
    {
        $careerPosition->update($this->validatedData($request));

        return redirect()->route('admin.career-positions.index')->with('success', 'Updated successfully');
    }

    public function destroy(CareerPosition $careerPosition)
    {
        $careerPosition->delete();

        return redirect()->route('admin.career-positions.index')->with('success', 'Deleted successfully');
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'department_ar' => ['nullable', 'string', 'max:255'],
            'department_en' => ['nullable', 'string', 'max:255'],
            'location_ar' => ['nullable', 'string', 'max:255'],
            'location_en' => ['nullable', 'string', 'max:255'],
            'job_type' => ['required', 'in:full_time,part_time,internship,contract'],
            'experience_level' => ['nullable', 'string', 'max:255'],
            'summary_ar' => ['nullable', 'string'],
            'summary_en' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'requirements_ar' => ['nullable', 'string'],
            'requirements_en' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }
}
