<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Support\HandlesImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogPostController extends Controller
{
    use HandlesImageUpload;

    public function index()
    {
        $posts = BlogPost::query()->with('category')->latest()->paginate(15);

        return view('admin.blog.index', compact('posts'));
    }

    public function create()
    {
        $categories = BlogCategory::query()->orderBy('name_en')->get();

        return view('admin.blog.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'blog_category_id' => ['nullable', 'exists:blog_categories,id'],
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'content_ar' => ['required', 'string'],
            'content_en' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:blog_posts,slug'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['title_en']);
        $data['image'] = $this->storeImage($request->file('image'), 'blog');

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        BlogPost::create($data);

        return redirect()->route('admin.blog-posts.index')->with('success', 'Saved successfully');
    }

    public function show(BlogPost $blogPost)
    {
        return view('admin.blog.show', ['post' => $blogPost]);
    }

    public function edit(BlogPost $blogPost)
    {
        $categories = BlogCategory::query()->orderBy('name_en')->get();

        return view('admin.blog.edit', ['post' => $blogPost, 'categories' => $categories]);
    }

    public function update(Request $request, BlogPost $blogPost)
    {
        $data = $request->validate([
            'blog_category_id' => ['nullable', 'exists:blog_categories,id'],
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'content_ar' => ['required', 'string'],
            'content_en' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:blog_posts,slug,' . $blogPost->id],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['title_en']);
        $data['image'] = $this->storeImage($request->file('image'), 'blog', $blogPost->image);

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $blogPost->update($data);

        return redirect()->route('admin.blog-posts.index')->with('success', 'Updated successfully');
    }

    public function destroy(BlogPost $blogPost)
    {
        $blogPost->delete();

        return redirect()->route('admin.blog-posts.index')->with('success', 'Deleted successfully');
    }
}
