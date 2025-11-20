<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PrizeProbabilitiesController extends Controller
{
    
    public function index(Request $request){

        return view('prizee.index');
    }

    public function create(Request $request){
        return view('prizee.create'); 
    }
}
