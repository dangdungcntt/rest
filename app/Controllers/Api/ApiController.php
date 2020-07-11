<?php

namespace App\Controllers\Api;

use Core\Contracts\Singleton;

class ApiController implements Singleton
{
    public function index()
    {
        return [
            'email' => 'dangdungcntt@gmail.com',
            'name'  => 'Dung Nguyen Dang'
        ];
    }
}