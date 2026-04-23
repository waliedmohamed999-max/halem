<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Branch;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::query()->with('workingHours')->where('is_active', true)->orderBy('sort_order')->get();

        return view('front.branches.index', compact('branches'));
    }

    public function show(int $id)
    {
        $branch = Branch::query()->with(['workingHours', 'doctors'])->findOrFail($id);

        return view('front.branches.show', compact('branch'));
    }
}
