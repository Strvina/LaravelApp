<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        return view('pages.contact');
    }

    public function send(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        Mail::to('vladimir.nicic02@gmail.com')->send(
            new ContactMessage($request->only('name', 'email', 'message'))
        );

        return back()->with('success', 'Message sent successfully. Thanks for reaching out.');
    }
}
