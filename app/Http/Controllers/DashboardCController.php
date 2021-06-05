<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardCController extends Controller
{

    public function dashboard(){
        return view('dashboard');
    }
}
