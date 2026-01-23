<?php

namespace App\Http\Controllers;
use App\Mail\ContactMessage;
use Illuminate\Http\Request;
use Mail;

class ContactController extends Controller
{
    public function index()
    {
        return view("pages.contact");
    }

    public function send(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        // Pošalji email
        Mail::to('vladimir.nicic02@gmail.com')->send(new ContactMessage($request->only('name','email','message')));

        return back()->with('success', 'Your message has been sent!');
    }
}
