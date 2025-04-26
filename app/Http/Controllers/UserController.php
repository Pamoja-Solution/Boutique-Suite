<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    //

    public function dashboard(){
        return view('vendeur.dashboard');
    }
    public function dashboardgerant(){
        return view('vendeur.dashboard');
    }

    public function dashboardsuperviseur(){
        return view('superviseur.dashboard');
    }
}
