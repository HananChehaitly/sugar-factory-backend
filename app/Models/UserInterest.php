<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInterest extends Model{
	protected $table = "user_interests";
	
	function interestOf(){
		return $this->belongsTo(User::class, 'id');
	}
}


?>