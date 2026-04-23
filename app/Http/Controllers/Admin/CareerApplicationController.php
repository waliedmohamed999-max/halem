<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CareerApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CareerApplicationController extends Controller
{
    public function index(Request $request)
    {
        $applications = CareerApplication::query()
            ->with('position')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.career-applications.index', compact('applications'));
    }

    public function show(CareerApplication $careerApplication)
    {
        $careerApplication->load('position');

        return view('admin.career-applications.show', compact('careerApplication'));
    }

    public function update(Request $request, CareerApplication $careerApplication)
    {
        $data = $request->validate([
            'status' => ['required', 'in:new,reviewed,interview,rejected,hired'],
            'admin_notes' => ['nullable', 'string', 'max:3000'],
        ]);

        $careerApplication->update($data);

        return back()->with('success', 'Updated successfully');
    }

    public function destroy(CareerApplication $careerApplication)
    {
        if ($careerApplication->cv_file) {
            Storage::disk('public')->delete($careerApplication->cv_file);
        }

        $careerApplication->delete();

        return redirect()->route('admin.career-applications.index')->with('success', 'Deleted successfully');
    }
}
