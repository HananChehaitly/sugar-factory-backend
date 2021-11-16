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

class adminController extends Controller
{
    public function getMessages(){
        $message = UserMessage::unApproved()->get();
        return response()->json($message, 200);
    }

    public function approveMessage(Request $request){
        $message = UserMessage::unApproved()->find($request->id);
        $message->is_approved = 1;
        $message->save();
        $response['status'] = "message approved";
        return response()->json($response, 200);
    }

    public function declineMessage(Request $request){
        $message = UserMessage::unApproved()->find($request->id);
        $message->delete();
        $response['status'] = "message declined";
        return response()->json($response, 200);
    }

    public function getImages(){
        $images = UserPicture::unApproved()->get();
        return response()->json($images, 200);
    }

    public function approvetImage(Request $request){
        $image = UserPicture::unApproved()->find($request->id);
        $image->is_approved = 1;
        $image->save();
        $response['status'] = "image approved";
        return response()->json($response, 200);
    }

    public function declineImage(Request $request){
        $image = UserPicture::unApproved()->find($request->id);
        $image->delete();
        $response['status'] = "image declined";
        return response()->json($response, 200);
    }
}
