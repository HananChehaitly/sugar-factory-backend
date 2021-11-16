<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserHobby extends Model{
	protected $table = "user_hobbies";
	
	function foruser(){
		return $this->belongsTo(User::class, 'id');
	}

}


?>