<?php

namespace App\Http\Controllers;

use App\Models\FrontendSection;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebController extends Controller
{

    public function index(): View
    {
        $title = "Home";
        return view('frontend.home', compact('title'));
    }
    public function about(): View
    {
        $title = "About";
        return view('frontend.pages.about', compact('title'));
    }
    public function features(): View
    {
        $title = "Features";
        return view('frontend.pages.features', compact('title'));
    }
    public function pricing(): View
    {
        $title = "Pricing";
        return view('frontend.pages.pricing', compact('title'));
    }
    public function faq(): View
    {
        $title = "Faq";
        return view('frontend.pages.faq', compact('title'));
    }
  


    public function pages($key , $id): View
    {
        $title = "Pages";
        $data = FrontendSection::where('id',$id)->first();
        $description = getArrayValue($data ->section_value, 'details');

        return view('frontend.pages', compact('title',"description",'key'));
    }
}
