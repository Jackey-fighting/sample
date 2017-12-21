<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{//Eloquent默认是模型的复数小写为表名，并自动连接，除非你用了table属性：protected $table=''
 //protected $connection = 'connection_name'表示要连接的数据库名
	public function user(){
		return $this->belongsTo('App\Models\User','user_id','id');
	}
}
