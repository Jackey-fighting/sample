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
        $user_ids = Auth::user()->followings->pluck('id')->toArray();//pluck('id')分离出id的所有值
        array_push($user_ids, Auth::user()->id);
        return Status::whereIn('user_id', $user_ids)
                        ->with('user')
                        ->orderBy('create_at', 'desc');
    }
    //用户粉丝关注
    public function followers(){
        
        return $this->belongsToMany(User::Class, 'followers', 'user_id','follower_id');
    }

    public function followings(){
        return $this->belongsToMany(User::Class, 'followers', 'follower_id', 'user_id');
    }
    //对用户进行关注
    public function follow($user_ids){
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->sync($user_ids, false);
    }
    //取消关注
    public function unfollow($user_ids){
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->detach($user_ids);
    }
    //判断A用户是否关注了B用户
    public function isFollowing($user_id){
        return $this->followings->contains($user_id);
    }

    //测试一对一
    public function phone(){//hasOne(关联的类型, 关联类型的外键（默认是user_id）, 本地的外键（默认是id）)
        return $this->hasOne('App\Models\Phone','user_id','id');
    }

    //自测一对多
    public function hasPoneMany(){
        return $this->hasMany('App\Models\Phone','user_id','id');
    }
}
