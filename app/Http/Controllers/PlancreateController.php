<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PlancreateController extends Controller
{
    public function index()
    {
        return view('plancreater.plancrud');
    }
}
