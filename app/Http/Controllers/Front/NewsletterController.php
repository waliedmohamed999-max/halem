<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'unique:subscribers,email'],
        ]);

        Subscriber::create($data);

        return back()->with('success', 'Subscribed successfully');
    }
}
