<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller
{
	public function __construct(){
		$this->middleware('guest',[
			'only'=>['create']
		]);
	}

    public function create(){
    	return view('sessions.create');
    }

    public function store(Request $request){
    	$credentials = $this->validate($request,[
    		'email'=>'required|email|max:255',
    		'password'=>'required'
    	]);

    	if (Auth::attempt($credentials,$request->has('remember'))) {
            if (Auth::user()->activated) {
                
        		//登录成功后的操作
        		session()->flash('success','Welcome to here.');
        		return redirect()->intended(route('users.show',[Auth::user()]));
            }else{
                Auth::logout();
                session()->flash('warning', '你的账号未激活，请检查邮箱中的注册邮件进行激活');
            }
    	}else{
    		//登录失败后的操作
    		session()->flash('danger','很抱歉，您的邮箱和密码不匹配');
    		return redirect()->back();
    	}
    }

    //退出登录
    public function destroy(){
    	Auth::logout();//Laravel自带的Auth::logout()退出
    	session()->flash('success','您已经成功退出。');
    	return redirect('login');
    }
}
