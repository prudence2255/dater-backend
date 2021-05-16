<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Resources\UsersResource;
use Illuminate\Support\Facades\DB;

class FriendController extends Controller
{

    public function __construct(){
        $this->middleware('auth:client');
    }

    /**
     * get friends
     *
     * @return void
     */
    public function getFriends($username){
        $user = Client::where('username', $username)->first();
        return UsersResource::collection($this->mergeFriends($user));
    }


    /**
     * get auth user friends
     *
     * @return void
     */
    public function getAuthUserFriends(){
        $user = request()->user();
        return UsersResource::collection($this->mergeFriends($user));
    }


    /**
     * get  requests sent to you
     *
     * @return void
     */
    public function getFriendRequests(){
        $user = request()->user();
        return UsersResource::collection($user->friends()->where('status', 'pending')->get());
    }


    /**
     * get  requests you sent
     *
     * @return void
     */
    public function myFriendRequests(){
        $user = request()->user();
        return UsersResource::collection($user->myFriends()->where('status', 'pending')->get());
    }


    /**
     * send a friend request
     *
     * @return void
     */
    public function sendFriendRequest(){
        $request = Friend::create([
            'sender_id' => request()->user()->id,
            'accepter_id' => request()->client_id,
            'status' => 'pending'
        ]);

        return response()->json($request);
    }

    /**
     * accepts a friend request
     *
     * @return void
     */
    public function acceptFriendRequest(){
        $request = Friend::where('accepter_id', request()->user()->id)
                            ->where('sender_id', request()->client_id)
                            ->first();

        $request->read_at = now();
        $request->status = 'accepted';
        $request->save();
        return response()->json($request);
    }


    /**
     * rejects a friend request
     */
    public function rejectFriendRequest(){
        $request = Friend::where('accepter_id', request()->user()->id)
                    ->where('sender_id', request()->client_id)
                    ->first();

        if($request){
            $request->delete();
        }
        return response()->json($request);
         }



         /**
          * display the merged friends
          *
          * @param [type] $user
          * @return void
          */
   public function mergeFriends($user){
            $friends = Friend::where('accepter_id', $user->id)
            ->orWhere('sender_id', $user->id)
            ->where(function($query){
                $query->where('status', 'accepted');
            })
            ->paginate(10);

        $friends = $friends->map(function($friend){
        return [$friend->myFriend, $friend->friendOfMe];
        });

        $friends = array_filter($friends[0] ?? [], function($friend) use($user){
            if($user->id === request()->user()->id){
                return $friend->id !== request()->user()->id;
            }else{
                return $friend->id !== $user->id;
            }
        });

        return $friends;
   }


}
