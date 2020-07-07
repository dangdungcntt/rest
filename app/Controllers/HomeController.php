<?php

namespace App\Controllers;

class HomeController
{
    public function __invoke()
    {
        return view('home.twig');
    }

    public function home()
    {
        return response()->redirect('/');
    }
}