<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
	
	
	public function getFullNameAttribute(){
		return implode(' ', [$this->first_name, $this->last_name]);
	}

       /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }
    
    public function pictures(){
        return $this->hasMany(UserPicture::class, 'user_id');
    }

    public function connection(){
        return $this->belongsToMany(User::class, 'user_connections', 'user1_id', 'user2_id');
    }
	
    public function favorite(){
        return $this->belongsToMany(User::class, 'user_favorites', 'from_user_id', 'to_user_id');
    }

    public function blockUser(){
        return $this->belongsToMany(User::class, 'user_blocked', 'from_user_id', 'to_user_id');
    }

    public function interests(){
        return $this->hasMany(UserInterest::class, 'user_id');
    }

    public function messages(){
        return $this->hasMany(UserMessage::class, 'user_id');
    }

    public function favorites(){
        return $this->hasMany(UserFavorite::class, 'user_id');
    }

    public function hobbies(){
        return $this->hasMany(UserHobby::class, 'user_id');
    }

    public function notifications(){
        return $this->hasMany(UserNotification::class, 'user_id');
    }
	
    public function scopeIsUser($query){
        return $query->where('user_type_id','=',2);
    }

    public function scopeNotMe($query, $id){
        return $query->where('id','<>', $id);
    }

    public function scopeInterestIn($query, $interest){
        return $query->where('gender','=', $interest);
    }

    public function scopeSearch($query, $name){
        return $query->where('first_name','LIKE' ,"$name")
                    ->orWhere('last_name','LIKE' , "$name");
    }

   
}
