<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function create(){//user sign up
    	return view('users.create');
    }
}
