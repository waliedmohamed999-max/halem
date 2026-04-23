<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::query()->latest()->paginate(15);

        return view('admin.branches.index', compact('branches'));
    }

    public function create()
    {
        return view('admin.branches.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'address_ar' => ['required', 'string'],
            'address_en' => ['required', 'string'],
            'google_maps_url' => ['nullable', 'url'],
            'phone' => ['nullable', 'string', 'max:50'],
            'working_hours' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        Branch::create($data);

        return redirect()->route('admin.branches.index')->with('success', 'Saved successfully');
    }

    public function show(Branch $branch)
    {
        return view('admin.branches.show', compact('branch'));
    }

    public function edit(Branch $branch)
    {
        return view('admin.branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $data = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'address_ar' => ['required', 'string'],
            'address_en' => ['required', 'string'],
            'google_maps_url' => ['nullable', 'url'],
            'phone' => ['nullable', 'string', 'max:50'],
            'working_hours' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $branch->update($data);

        return redirect()->route('admin.branches.index')->with('success', 'Updated successfully');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();

        return redirect()->route('admin.branches.index')->with('success', 'Deleted successfully');
    }
}
