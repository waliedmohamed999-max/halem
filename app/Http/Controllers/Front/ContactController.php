<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Setting;
use App\Models\WorkingHour;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $hours = WorkingHour::query()->whereNull('branch_id')->orderBy('day_of_week')->get();

        return view('front.contact', compact('hours'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        ContactMessage::create($data + ['status' => 'unread']);

        return back()->with('success', 'Message sent');
    }
}
