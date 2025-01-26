<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    // homepage
    public function index() {

        return view("pages/index", [
            //"title" => config("app.name")." :: Welcome",
            "title" => "Go to Vue.js Notes App",
        ]);
    }
}
