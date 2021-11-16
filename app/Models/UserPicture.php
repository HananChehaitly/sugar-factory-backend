<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPicture extends Model{
	protected $table = "user_pictures";
	
	function foruser(){
		return $this->belongsTo(User::class, 'id');
	}

	public function scopeUnApproved($query){
        return $query->where('is_approved', '0');
    }

}


?>