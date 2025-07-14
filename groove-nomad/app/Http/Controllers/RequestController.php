<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index()
    {
        // Logic for handling requests
        return view('request.index');
    }
}
