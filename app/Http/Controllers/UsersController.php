<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
	public function __construct(){
		$this->middleware('auth', [
			'except'=>['show','create','store','index']
		]);

		$this->middleware('guest',[
			'only'=>['create']
		]);
	}

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
    	Auth::login($user);
    	session()->flash('success','欢迎，您将在这里开启一段新的旅程');
    	return redirect()->route('users.show',[$user]);//等同于route('users.shwo',[$user->id])
    }

    //编辑用户的操作
    public function edit(User $user){
    	$this->authorize('update',$user);
    	return view('users.edit',compact('user'));
    }

    //展示所有的用户
    public function index(){
    	$users = User::paginate(10);//paginate()指定每页生成的数量
    	return view('users.index',compact('users'));
    }

    public function update(User $user,Request $request){
    	$this->validate($request, [
    		'name'=>'required|max:50',
    		'password'=>'nullable|confirmed|min:6'
    	]);

    	$this->authorize('update',$user);

    	$data=[];
    	$data['name']=$request->name;
    	if ($request->password) {
    		$data['password']=$request->password;
    	}

    	$user->update($data);
    	session()->flash('success','change password success.');
    	return redirect()->route('users.show',$user->id);
    }

    //进行用户的删除
    public function destroy(User $user){
    	$this->authorize('destroy',$user);
    	$user->delete();
    	session()->flash('success','成功删除用户');
    	return back();
    }
}
