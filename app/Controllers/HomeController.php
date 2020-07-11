<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface;

class HomeController
{
    public function __invoke(ServerRequestInterface $request)
    {
        return view('home.twig');
    }

    public function home()
    {
        return response()->redirect('/');
    }
}