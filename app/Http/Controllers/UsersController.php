<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function create(){//user sign up
    	return view('users.create');
    }

    public function show(User $user){
    	return view('users.show',compact('user'));
    }

    public function store(Request $request){
    	$this->validate($request, [
    			'name'=>'required|max:50',
    			'email'=>'required|email|unique:users|max:255',
    			'password'=>'required|confirmed'
    		]);

    	$user = User::create([
    	'name'=>$request->name,//因为表单有name字段，所以可以这样取值，想要取所有值可以$request->all()
    	'email'=>$request->email,
    	'password'=>bcrypt($request->password),
    ]);

    	session()->flash('success','欢迎，您将在这里开启一段新的旅程');
    	return redirect()->route('users.show',[$user]);//等同于route('users.shwo',[$user->id])
    }

}
