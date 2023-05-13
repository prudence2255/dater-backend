<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Events\Viewed;
use App\Models\Client;
use App\Events\NewMessage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProfilePicture;
use App\Notifications\Welcome;
use App\Http\Traits\MediaUpload;
use App\Http\Traits\Filters;
use App\Http\Requests\MetaRequest;
use App\Http\Requests\ClientRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UsersResource;
use Illuminate\Notifications\Notifiable;
use App\Http\Requests\UpdateClientRequest;


class ClientController extends Controller
{

    use MediaUpload, Notifiable, Filters;


    public function __construct()
    {
        $this->middleware('auth:client')->except([
            'store', 'clientLogin', 'test'
        ]);
    }

    public function test()
    {
        echo json_encode('tested');
        return;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $start_query = Client::query();

        $query = $this->filters($start_query);

        return UsersResource::collection($query->whereNotIn('id', [request()->user()->id])->paginate(10));
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
        $username = Str::slug($request->first_name) . bin2hex(random_bytes(5));
        $merge = $collection->merge(compact('username', 'password'));
        $client = Client::create($merge->all());
        $client->username = Str::slug($request->first_name) . bin2hex($client->id);
        $client->active = '1';
        $client->save();
        $token = $client->createToken('token')->accessToken;

        //send a welcome message to a newly registered user
        $client->notify(new Welcome('Registered'));

        return response()->json([
            'data' => $client,
            'token' => $token,
            'welcome' => $client->notifications->first(function ($n) {
                return $n->type === "App\Notifications\Welcome";
            })
        ]);
    }

    /**
     * logs client in
     *
     * @return void
     */
    public function clientLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);
        $remember_me = $request->has('remember_me') ? true : false;

        $client = Client::where('email', $request->email)->first();
        if ($client) {
            if (Hash::check($request->password, $client->password)) {
                $token = $client->createToken('token')->accessToken;
                $client->active = '1';
                $client->save();
                return response()->json(['data' => $client, 'token' => $token]);
            } else {
                return response()->json(
                    [
                        'errors' => (object) ['error' => ['Email or password invalid']]
                    ],
                    422
                );
            }
        } else {
            return response()->json(
                [
                    'errors' => (object) ['error' => ['Email or password invalid']]
                ],
                422
            );
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

        if (request()->user()->id !== $client->id) {
            Viewed::dispatch($client);
        }

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

    public function createOrUpdateMeta(MetaRequest $request)
    {
        $user = $request->user();
        if (!$user->meta) {
            $user->meta()->create($request->all());
        } else {
            $user->meta()->update($request->all());
        }

        return response()->json(new UserResource($user));
    }


    /**
     * logs client out
     */
    public function clientLogout(Request $request)
    {
        $user =  $request->user();
        $user->active = '0';
        $user->save();
        $user->token()->revoke();

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

    public function photoUpload()
    {
        $photo = $this->save_photo();
        $savePhoto = ['photos' => $photo];

        $userPhoto = request()->user()->photos()->create($savePhoto);

        return response()->json($userPhoto);
    }


    /**
     * retrieve user photos
     *
     * @return void
     */
    public function getPhotos($username)
    {
        $user = Client::where('username', $username)->first();
        $photos = collect($user->photos);
        $profile_pics = collect($user->profilePictures);
        $merge = $photos->merge($profile_pics);

        return response()->json($merge->all());
    }

    /**
     * upload profile picture
     */
    public function uploadProfilePic(Request $request)
    {
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

    public function authUser()
    {
        $user = new UserResource(Client::findOrFail(request()->user()->id));

        return response()->json([
            'user' => $user,
            'new_messages_count' => $user->unreadMessagesCount(),
            'new_notifications_count' => $user->unreadNotifications->count(),
            'new_views_count' => $user->views()->where('read_at', null)->count(),
            'new_likes_count' => $user->likers()->where('read_at', null)->count(),
            'new_friends_count' => $user->friends()->where('status', 'pending')->count(),
            'notifications_count' => $this->notificationsCount($user),
        ]);
    }

    /**
     * get the total number of notifications
     */

    public function notificationsCount($user)
    {
        return $user->unreadMessagesCount() +
            $user->unreadNotifications->count()
            + $user->views()->where('read_at', null)->count()
            + $user->likers()->where('read_at', null)->count()
            + $user->friends()->where('status', 'pending')->count();
    }
}
