<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Mail;

class UsersController extends Controller
{
	public function __construct(){
		$this->middleware('auth', [
			'except'=>['show','create','store','index','confirmEmail']
		]);

		$this->middleware('guest',[
			'only'=>['create']
		]);
	}

    public function create(){//user sign up
    	return view('users.create');
    }

    public function show(User $user){
    	$statuses = $user->statuses()->orderBy('created_at','desc')->paginate(30);
    	return view('users.show',compact('user','statuses'));
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
    	$this->sendEmailConfirmationTo($user);
    	session()->flash('success','验证邮件已经发送到你的注册邮箱上，请注意查看。');
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

    //进行邮箱的发送
    protected function sendEmailConfirmationTo($user){
    	$view = 'emails.confirm';
    	$data = compact('user');
    	$from = 'aufree@yousails.com';
    	$name = 'Aufree';
    	$to = $user->email;
    	$subject = "感谢注册 Sample 应用！请确认你的邮箱。";

    	Mail::send($view,$data,function($message) use ($from, $name, $to, $subject){
    		$message->from($from,$name)->to($to)->subject($subject);
    	});
    }

    //进行邮箱的验证
    public function confirmEmail($token){
    	$user = User::where('activation_token',$token)->firstOrFail();

    	$user->activated = true;
    	$user->activation_token = null;
    	$user->save();

    	Auth::login($user);
    	session()->flash('success', '恭喜你，激活成功！');
    	return redirect()->route('users.show', [$user]);
    }
}
