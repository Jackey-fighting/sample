<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class FollowersController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    }
    //进行用户关注
    public function store(User $user){
    	if (Auth::user()->id === $user->id) {
    		return redirect('/');
    	}

    	if (!Auth::user()->isFollowing($user->id)) {
    		Auth::user()->follow($user->id);
    	}

    	return redirect()->route('users.show', $user->id);
    }
    //进行用户取消操作
    public function destroy(User $user){
    	if (Auth::user()->id === $user->id) {
    		return redirect('/');
    	}

    	if (Auth::user()->isFollowing($user->id)) {
    		Auth::user()->unfollow($user->id);
    	}

    	return redirect()->route('users.show', $user->id);
    }
}
