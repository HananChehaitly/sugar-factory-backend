<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserPicture;
use App\Models\UserMessage;
use App\Models\UserInterest;
use App\Models\UserHobby;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Storage;

class userController extends Controller
{
    public function addPicture(Request $request){
        $image = $request->image;  // your base64 encoded
        $imageName = "str_random(".rand(10,1000).")".'.'.'jpeg';
        $path=public_path();
        \File::put($path. '/image/' . $imageName, base64_decode($image));
        $response['status'] = "add_favorite";
        $user_id = auth()->user()->id;
        $user = User::find($user_id);
        $user->p_path = '/image/'.$imageName;
        $user->save();

        return response()->json($user, 200);
    }

    public function addFavorite(Request $request){
        $user_id = auth()->user()->id;
        $user = User::find($user_id);
        $favorited_by_other = User::find($request->user_id)->favorite()->where('to_user_id',$user_id)->first();
        if($favorited_by_other){
            $user_to_favorite = User::find($request->user_id);
            $user->favorite()->save($user_to_favorite);
            $this->addConnection($user_id, $request->user_id);
            $second_user = User::find($request->user_id);
            $user_notification = new UserNotification;
            $user_notification->body = "$user->first_name $user->last_name has favorited you!";	
            $user_notification->is_read = 0;	
            $second_user->notifications()->save($user_notification);
            $user_notification = new UserNotification;
            $user_notification->body = "You and $user->first_name $user->last_name are now Connected!";	
            $user_notification->is_read = 0;	
            $second_user->notifications()->save($user_notification);
            $user_notification->body = "You and $second_user->first_name $second_user->last_name are now Connected!";	
            $user->notifications()->save($user_notification);
            $response['action'] = 'Connection createrd';
            $response['status'] = "add_favorite";
            return response()->json($response, 200);
        }
        $user_to_favorite = User::find($request->user_id);
        $user->favorite()->save($user_to_favorite);
        $user_notification = new UserNotification;
        $user_notification->body = "$user->first_name $user->last_name has favorited you!";	
		$user_notification->is_read = 0;	
        $user_to_favorite->notifications()->save($user_notification);
        $response['status'] = "add_favorite";
        return response()->json($response, 200);
        
    }

    public function addConnection($first_user_id, $second_user_id){
        $user = User::find($first_user_id);
        $user_to_add = User::find($second_user_id);
        $user->connection()->save($user_to_add);
    }

    public function removeConnection(Request $request){
        $user_id = auth()->user()->id;
        $user = User::find($user_id);
        $user_to_remove = User::find($request->user_id);
        $connection = $user->connection()->find($user_to_remove);
        if($connection){
            $connection->pivot;
        }else{
            $connection = $user_to_remove->connection()->find($user)->pivot;
        }
        $connection->delete();
        $response['status'] = "connection_removed";
        return response()->json($response, 200);
            
    }

    public function blockUser(Request $request){
        $user_id = auth()->user()->id;
        $user = User::find($user_id);
        $user_to_block = User::find($request->user_id);
        $user->blockUser()->save($user_to_block);
        $response['status'] = "user_blocked";
        return response()->json($response, 200);
    }

    public function sendMessage(Request $request){
        $user_id = auth()->user()->id;
        $messagefrom = UserMessage::find($request->receiver_id);
        $reciever_id = $messagefrom->sender_id;
        $first_user = User::find($user_id);
        $second_user = User::find($reciever_id);
        $connection_exist = $first_user->connection()->where([['user1_id', '=', $first_user->id],
                                                              ['user2_id', '=' , $second_user->id]
                                                             ]) 
                                                     ->orWhere([['user2_id', '=', $first_user->id],
                                                                ['user1_id', '=' , $second_user->id]
                                                               ])
                                                    ->first();
        if($connection_exist){
            $message = new UserMessage;
            $message->sender_id = $user_id;
            $message->receiver_id= $reciever_id;
            $message->body= $request->body;
            $message->is_approved= 0;	
            $message->is_read= 0;
            $message->save();
            $response['status'] = "message_sent";
            return response()->json($response, 200);
        }else{
            $response['status'] = "access denied";
            return response()->json($response, 403);
        }
    }

    public function addInterest(Request $request){
        $user_id = auth()->user()->id;
        $user = User::find($user_id);
        $user_interest = new UserInterest;
        $user_interest->name = $request->name;
        $user->interests()->save($user_interest);
        return response()->json($user_interest, 200);
    }

    public function editInterest(Request $request){
        $user_id = auth()->user()->id;
        $user = User::find($user_id);
        $user_interest = $user->interests()->find($request->interest_id);
        $user_interest->name = $request->name;
        $user_interest->save();
        return response()->json($user_interest, 200);
    }

    public function getInterests(Request $request){
        $user_id = auth()->user()->id;
        $user = User::find($user_id);
        $user_interests = $user->interests()->get();
        return response()->json($user_interests, 200);
    }


    public function addHobby(Request $request){
        $user_id = auth()->user()->id;
        $user = User::find($user_id);
        $user_hobby = new UserHobby;
        $user_hobby->name = $request->name;
        $user->hobbies()->save($user_hobby);
        return response()->json($user_hobby, 200);
    }

    public function editHobby(Request $request){
        $user_id = auth()->user()->id;
        $user = User::find($user_id);
        $user_hobby = $user->hobbies()->find($request->hobby_id);
        $user_hobby->name = $request->name;
        $user_hobby->save();
        return response()->json($user_hobby, 200);
    }

    public function getHobbies(Request $request){
        $user_id = auth()->user()->id;
        $user = User::find($user_id);
        $user_hobbies = $user->hobbies()->get();
        return response()->json($user_hobbies, 200);
    }

    public function addNotification(Request $request){
        $user_id = auth()->user()->id;
        $user = User::find($user_id);
        $user_notification = new UserNotification;
        $user_notification->body = $request->body;	
		$user_notification->is_read = 0;	
        $user->notifications()->save($user_notification);
        return response()->json($user_notification, 200);
    }

    public function searchUser(Request $request){
        $user_id = auth()->user()->id;
        $user = User::find($user_id);
        $name = '%'.$request->name."%";
        $users = User::Search($name)
                       ->interestIn($user->interested_in)
                       ->isUser()
                       ->notMe($user_id)
                       ->get();
        if(count($users) > 0){
            return response()->json($users, 200);
        }else{
            $response['status'] = "No results found";
            return response()->json($response, 200);
        }
    }

    public function getAllMatches(Request $request){
        $user_id = auth()->user()->id;
        $user = User::find($user_id);
        $users = $user->connection()->where('user1_id', $user_id)
                                    ->orWhere('user2_id', $user_id)
                                    ->get();
        return response()->json($users, 200);                   
    }

    public function editProfile(Request $request){
        $image_64 = $request->base;
        // try{
        //     $image_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image_64));
        // }catch (Exception $e) {
        //    $error =  $e->getMessage();
        //    return response()->json($error, 200);
        // }
        // $f = finfo_open();
        // $mime_type = finfo_buffer($f, $image_data, FILEINFO_MIME_TYPE);
        // $imageName = time().'.'.$mime_type;
        // $image_data->move(public_path('image'), $imageName );
        // $image = str_replace('data:image/png;base64,', '', $image);
        // $image = str_replace(' ', '+', $image);
        $user_id = auth()->user()->id;
        $user = User::find($user_id);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->gender = $request->gender;
        $user->interested_in = $request->interested_in;
        $user->dob = $request->dob;
        $user->height = $request->height;
        $user->weight = $request->weight;
        $user->nationality = $request->nationality;
        $user->net_worth = $request->net_worth;
        $user->currency = $request->currency;
        $user->bio = $request->bio;
        $user->save();
        return response()->json($image_64, 200);                   
    }

    public function getUser(Request $request){
        $user = User::find($request->id);
        if($user){
            return response()->json($user, 200);
        }else{
            $response['status'] = "No results found";
            return response()->json($response, 200);
        }
    }

    public function getMessages(Request $request){
        $user_id = auth()->user()->id;
         $user = User::find($user_id);
         $messages = UserMessage::where([
                                ['receiver_id','=', $user_id],
                                ['is_approved','=',1],
                                ['is_read','=',0]
                                ])
                                 ->get();
         foreach($messages as $msg){
             $sender_id = $msg->sender_id;
             $sender = User::find($sender_id);
             $msg["first_name"] = $sender->first_name;
             $msg["last_name"] = $sender->last_name;
         }
         return response()->json($messages,200);
     }

     public function setMessageAsRead(Request $request){
        $message = UserMessage::find($request->message_id);
        $message->is_read = 1;
        $message->save();
        $response['status'] = "ignored";
        return response()->json($response);
     }

     public function getUserNotificaion(Request $request){
        $user_id = auth()->user()->id;
        $notification = User::find($user_id)->notifications()->where('is_read',0)->get();
        return response()->json($notification);
     }
     
     public function readNotificaion(Request $request){
        $user_id = auth()->user()->id;
        $notification = User::find($user_id)->notifications()->find($request->id);
        $notification->is_read=1;
        $notification->save();
        return response()->json($notification);
     }

}
