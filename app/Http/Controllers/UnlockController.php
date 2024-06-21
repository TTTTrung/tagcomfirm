<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UnlockController extends Controller
{
    public function index(){
        return view('scanner.unlockscan'); 
       } 
}
