<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\UsersResource;

class ViewsController extends Controller
{


    public function __construct(){
        $this->middleware('auth:client');
    }

/**
 * returns views for the current user
 *
 * @return void
 */
    public function getViewers(){
        $user = request()->user();

        $user->views->each(function($view){
            if($view->read_at === null){
            $view->read_at = now();
            $view->save();
            }
        });

        return UsersResource::collection($user->viewers()->paginate(10));

    }


    public function unreadViews(){
        $user = request()->user();

        $count = $user->views()->where('read_at', null)->count();

        return response()->json($count);

    }
}
