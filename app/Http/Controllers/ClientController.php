<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Photo;
use App\Models\ProfilePicture;
use Illuminate\Http\Request;
use App\Http\Requests\ClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Requests\MetaRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Traits\MediaUpload;
use App\Events\NewMessage;
use App\Http\Resources\UserResource;
use App\Http\Resources\UsersResource;


class ClientController extends Controller
{

    use MediaUpload;

    public function __construct(){
        $this->middleware('auth:client')->except([
             'store', 'clientLogin', 'show', 'getPhotos'
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

       return UsersResource::collection(Client::whereNotIn('id', [request()->user()->id])->paginate(10));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(ClientRequest $request)
    {
        $password = Hash::make($request->password);
        $collection = collect($request->all());
        $username = Str::slug($request->first_name).bin2hex(random_bytes(5));
        $merge = $collection->merge(compact('username', 'password'));
       $client = Client::create($merge->all());
       $token = $client->createToken('token')->accessToken;

       return response()->json(['data' => $client, 'token' => $token]);
    }

    /**
     * logs client in
     *
     * @return void
     */
 public function clientLogin(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);
        $remember_me = $request->has('remember_me') ? true : false;

        $client = Client::where('email', $request->email)->first();
       if($client){
           if(Hash::check($request->password, $client->password)){
            $token = $client->createToken('token')->accessToken;
            return response()->json(['data' => $client, 'token' => $token]);
           }else{
            return response()->json([
                    'errors' => (Object) ['error' => ['Email or password invalid']]],
                             422);
             }
       }else{
        return response()->json([
                'errors' => (Object) ['error' => ['Email or password invalid']]],
                         422);
            }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function show(Client $client)
    {
        $user = new UserResource($client);
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateClientRequest $request, Client $client)
    {
        $client->update($request->all());

        $user = new UserResource($client);

        return response()->json($user);
    }

/**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function updateAuthUser(UpdateClientRequest $request)
    {
        $user = request()->user();
        $user->update($request->all());

        return response()->json(new UserResource($user));
    }


    /**
    * create or update client meta
     */

    public function createOrUpdateMeta(MetaRequest $request){
        $user = $request->user();
        if(!$user->meta){
        $user->meta()->create($request->all());
        }else{
          $user->meta()->update($request->all());
        }

        return response()->json( new UserResource($user));
    }


    /**
     * logs client out
     */
    public function clientLogout(Request $request){
        $request->user()->token()->revoke();

        return response()->json(['status', 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy(Client $client)
    {
        $client->delete_photos();
        $client->delete();
        return response()->json(['status' => 'success']);
    }


    /**
     * photos upload
     */

     public function photoUpload(){
         $photo = $this->save_photo();
        $savePhoto = ['photos' => $photo];

         $userPhoto = request()->user()->photos()->create($savePhoto);
        //  Photo::create([
        //     'client_id' => request()->user()->id,
        //     'photos' => $photo
        //  ]);

        return response()->json($userPhoto);
     }


     /**
      * retrieve user photos
      *
      * @return void
      */
     public function getPhotos($username){
        $user = Client::where('username', $username)->first();
        $photos = collect($user->photos);
        $profile_pics = collect($user->profilePictures);
        $merge = $photos->merge($profile_pics);

        return response()->json($merge->all());
     }

     /**
      * upload profile picture
      */
     public function uploadProfilePic(Request $request){
     $photo = $this->save_photo();
        ProfilePicture::create([
           'client_id' => request()->user()->id,
           'photos' =>  $photo
        ]);

       return response()->json($photo);

     }


     /**
      * get logged in user
      */

     public function authUser(){
       $user = new UserResource(Client::findOrFail(request()->user()->id));
        return response()->json($user);
     }



}
