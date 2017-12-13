<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPassword;
class User extends Authenticatable
{
    use Notifiable;
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function boot(){//boot()会在类模型加载完后进行加载，所以creating要放在boot中比较好
        parent::boot();

        static::creating(function($user){
            $user->activation_token = str_random(30);
        });
    }

    //生成用户头像
    public function gravatar($size='100'){
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }

    //指明一个用户拥有多条微博信息
    public function statuses(){
        return $this->hasMany(Status::class);
    }

    //调用notifications的发送邮件
    public function sendPasswordResetNotification($token){
        $this->notify(new ResetPassword($token));
    }
    //调取关注用户后，显示相关的微博信息
    public function feed(){
        return $this->statuses()->orderBy('created_at','desc');
    }
}
