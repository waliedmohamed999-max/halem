<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Support\HandlesImageUpload;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    use HandlesImageUpload;

    public function index()
    {
        $testimonials = Testimonial::query()->orderBy('sort_order')->paginate(20);

        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        return view('admin.testimonials.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'comment_ar' => ['required', 'string'],
            'comment_en' => ['nullable', 'string'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['image'] = $this->storeImage($request->file('image'), 'testimonials');
        $data['is_active'] = $request->boolean('is_active');
        Testimonial::create($data);

        return redirect()->route('admin.testimonials.index')->with('success', 'Saved successfully');
    }

    public function edit(Testimonial $testimonial)
    {
        return view('admin.testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'comment_ar' => ['required', 'string'],
            'comment_en' => ['nullable', 'string'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['image'] = $this->storeImage($request->file('image'), 'testimonials', $testimonial->image);
        $data['is_active'] = $request->boolean('is_active');
        $testimonial->update($data);

        return redirect()->route('admin.testimonials.index')->with('success', 'Updated successfully');
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();

        return redirect()->route('admin.testimonials.index')->with('success', 'Deleted successfully');
    }
}
