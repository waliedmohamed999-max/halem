<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\WorkingHour;
use Illuminate\Http\Request;

class WorkingHourController extends Controller
{
    public function index()
    {
        $hours = WorkingHour::query()->with('branch')->orderBy('branch_id')->orderBy('day_of_week')->paginate(50);

        return view('admin.working-hours.index', compact('hours'));
    }

    public function create()
    {
        $branches = Branch::query()->orderBy('sort_order')->get();

        return view('admin.working-hours.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        WorkingHour::create($data);

        return redirect()->route('admin.working-hours.index')->with('success', 'Saved successfully');
    }

    public function edit(WorkingHour $workingHour)
    {
        $branches = Branch::query()->orderBy('sort_order')->get();

        return view('admin.working-hours.edit', compact('workingHour', 'branches'));
    }

    public function update(Request $request, WorkingHour $workingHour)
    {
        $data = $this->validated($request);
        $workingHour->update($data);

        return redirect()->route('admin.working-hours.index')->with('success', 'Updated successfully');
    }

    public function destroy(WorkingHour $workingHour)
    {
        $workingHour->delete();

        return redirect()->route('admin.working-hours.index')->with('success', 'Deleted successfully');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'branch_id' => ['nullable', 'exists:branches,id'],
            'day_of_week' => ['required', 'integer', 'min:0', 'max:6'],
            'day_label_ar' => ['required', 'string', 'max:100'],
            'day_label_en' => ['required', 'string', 'max:100'],
            'is_open' => ['nullable', 'boolean'],
            'open_at' => ['nullable'],
            'close_at' => ['nullable'],
            'is_emergency' => ['nullable', 'boolean'],
            'emergency_text' => ['nullable', 'string', 'max:255'],
            'emergency_phone' => ['nullable', 'string', 'max:50'],
        ]);

        $data['is_open'] = $request->boolean('is_open');
        $data['is_emergency'] = $request->boolean('is_emergency');

        return $data;
    }
}
