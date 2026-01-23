<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomepageController extends Controller
{
    public function index()
    {
        $name  = "Vladimir";
        $lastName = "Nicic";
       //(prvi nacin) return view('homepage', compact('name', 'lastName'));
       //(drugi nacin)
       return view("pages.homepage", [
           //varijabla u bladeu => varijabla iz kontrolera
           'name'               =>             $name,
           'lastName'           =>             $lastName
       ]);
    }
}
