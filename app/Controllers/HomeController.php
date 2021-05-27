<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Rest\Application;

class HomeController
{
    public function __invoke(ServerRequestInterface $request): \Rest\Support\ViewResponse
    {
        $frameworkVersion = Application::VERSION;
        return view('home.twig', compact('frameworkVersion'));
    }

    public function home(): \Rest\Support\Response
    {
        return response()->redirect('/');
    }
}