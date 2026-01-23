<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class HomepageController extends Controller
{
    public function index()
    {
       //(prvi nacin) return view('homepage', compact('name', 'lastName'));
       //(drugi nacin)
       return view("pages.homepage", [
           //varijabla u bladeu => varijabla iz kontrolera
           'name'               =>             Auth::user()->name,
       ]);
    }
}
