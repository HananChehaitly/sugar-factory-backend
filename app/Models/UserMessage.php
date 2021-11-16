<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMessage extends Model{
	protected $table = "user_messages";
	
    function messagestOf(){
		return $this->belongsTo(User::class, 'id');
	}

	public function scopeUnApproved($query){
        return $query->where('is_approved', '0');
    }
}


?>