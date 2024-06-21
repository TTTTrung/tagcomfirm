<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ScanController extends Controller
{
   public function index(){
    return view('scanner.scantag'); 
   } 
}
