<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\CareerApplication;
use App\Models\CareerPosition;
use Illuminate\Http\Request;

class CareerController extends Controller
{
    public function index(Request $request)
    {
        $query = CareerPosition::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('id');

        if ($request->filled('department')) {
            $query->where(function ($builder) use ($request): void {
                $builder->where('department_ar', $request->string('department'))
                    ->orWhere('department_en', $request->string('department'));
            });
        }

        if ($request->filled('job_type')) {
            $query->where('job_type', $request->string('job_type'));
        }

        if ($request->filled('q')) {
            $keyword = trim((string) $request->input('q'));
            $query->where(function ($builder) use ($keyword): void {
                $builder->where('title_ar', 'like', "%{$keyword}%")
                    ->orWhere('title_en', 'like', "%{$keyword}%")
                    ->orWhere('summary_ar', 'like', "%{$keyword}%")
                    ->orWhere('summary_en', 'like', "%{$keyword}%");
            });
        }

        $positions = $query->paginate(9)->withQueryString();

        $departments = CareerPosition::query()
            ->where('is_active', true)
            ->select('department_ar', 'department_en')
            ->get()
            ->map(fn (CareerPosition $position): string => app()->getLocale() === 'ar'
                ? ($position->department_ar ?: $position->department_en)
                : ($position->department_en ?: $position->department_ar))
            ->filter()
            ->unique()
            ->values();

        $allPositions = CareerPosition::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return view('front.careers.index', compact('positions', 'departments', 'allPositions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'career_position_id' => ['nullable', 'exists:career_positions,id'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'city' => ['nullable', 'string', 'max:150'],
            'experience_years' => ['nullable', 'string', 'max:60'],
            'cover_letter' => ['nullable', 'string', 'max:4000'],
            'cv_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:3072'],
        ]);

        if (!empty($data['career_position_id'])) {
            $position = CareerPosition::query()
                ->whereKey($data['career_position_id'])
                ->where('is_active', true)
                ->first();
            if (! $position) {
                return back()->withInput()->withErrors([
                    'career_position_id' => app()->getLocale() === 'ar' ? 'الوظيفة المختارة غير متاحة حاليًا.' : 'Selected position is not available now.',
                ]);
            }
        }

        $cvPath = $request->file('cv_file')?->store('career-cv', 'public');

        CareerApplication::query()->create([
            'career_position_id' => $data['career_position_id'] ?? null,
            'full_name' => $data['full_name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'city' => $data['city'] ?? null,
            'experience_years' => $data['experience_years'] ?? null,
            'cover_letter' => $data['cover_letter'] ?? null,
            'cv_file' => $cvPath,
            'status' => 'new',
        ]);

        return redirect()
            ->route('front.careers.index', app()->getLocale())
            ->with('success', app()->getLocale() === 'ar' ? 'تم إرسال طلب التقديم بنجاح.' : 'Your application has been submitted successfully.');
    }
}
