<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model{
	protected $table = "user_notifications";
	
    function foruser(){
		return $this->belongsTo(User::class, 'id');
	}
}


?>