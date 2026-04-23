<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeSection;
use Illuminate\Http\Request;

class HomeSectionController extends Controller
{
    public function index()
    {
        $sections = HomeSection::query()->orderBy('sort_order')->paginate(20);

        return view('admin.home-sections.index', compact('sections'));
    }

    public function create()
    {
        return view('admin.home-sections.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'section_key' => ['required', 'string', 'max:100', 'unique:home_sections,section_key'],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'payload' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $payload = $data['payload'] ?? null;
        $data['payload'] = $payload ? json_decode($payload, true) : null;
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        HomeSection::create($data);

        return redirect()->route('admin.home-sections.index', app()->getLocale())->with('success', 'Saved successfully');
    }

    public function show($homeSection)
    {
        $homeSection = $this->resolveHomeSection($homeSection);

        return view('admin.home-sections.show', compact('homeSection'));
    }

    public function edit($homeSection)
    {
        $homeSection = $this->resolveHomeSection($homeSection);

        return view('admin.home-sections.edit', compact('homeSection'));
    }

    public function update(Request $request, $homeSection)
    {
        $homeSection = $this->resolveHomeSection($homeSection);

        $data = $request->validate([
            'section_key' => ['required', 'string', 'max:100', 'unique:home_sections,section_key,' . $homeSection->id],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'payload' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $payload = $data['payload'] ?? null;
        $data['payload'] = $payload ? json_decode($payload, true) : null;
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $homeSection->update($data);

        return redirect()->route('admin.home-sections.index', app()->getLocale())->with('success', 'Updated successfully');
    }

    public function destroy($homeSection)
    {
        $homeSection = $this->resolveHomeSection($homeSection);
        $homeSection->delete();

        return redirect()->route('admin.home-sections.index', app()->getLocale())->with('success', 'Deleted successfully');
    }

    private function resolveHomeSection(mixed $homeSection): HomeSection
    {
        if ($homeSection instanceof HomeSection) {
            return $homeSection;
        }

        return HomeSection::query()->findOrFail((int) $homeSection);
    }
}
