<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;

class AboutController extends Controller
{
    public function index()
    {
        // Get the about us content
        $about = AboutUs::first();  // This gets the first record

        // If no record exists, create an empty object to avoid errors
        if (!$about) {
            $about = new AboutUs();
        }

        return view('front.about', compact('about'));
    }
}
