<?php

namespace App\Http\Controllers;

use App\Models\Like;
use Illuminate\Http\Request;
use App\Http\Resources\UsersResource;

class LikeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:client');
    }

    /**
     * display users that likes you
     *
     * @return Response
     */
    public function getLikers()
    {
        $user = request()->user();

        $user->likes->each(function ($like) {
            if ($like->read_at === null) {
                $like->read_at = now();
                $like->save();
            }
        });

        return UsersResource::collection($user->likers()->paginate(10));
    }

    /**
     * display your liked users
     *
     * @return Response
     */
    public function getLikes()
    {
        $user = request()->user();
        return UsersResource::collection($user->myLikes()->paginate(10));
    }


    /**
     * saves a like
     *
     * @return Response
     */
    public function like()
    {
        $like =  Like::create([
            'client_id' => request()->client_id,
            'liker_id' => request()->user()->id,
        ]);

        return response()->json($like);
    }

    /**
     * deletes a like
     *
     * @return Response
     */
    public function unlike()
    {
        $like = Like::where('liker_id', request()->user()->id)
            ->where('client_id', request()->client_id)->first();

        if ($like) {
            $like->delete();
        }
        return response()->json('like deleted');
    }
}
