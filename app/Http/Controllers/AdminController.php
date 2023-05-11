<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api')->except(['adminLogin']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $admins = User::all();
        return response()->json($admins);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'c_password' => 'required|same:password'
        ]);
        $password = Hash::make($request->password);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $password,
            'username' => Str::slug($request->name) . bin2hex(random_bytes(5))
        ]);

        $token =  $user->createToken('token')->accessToken;

        return response()->json(['data' => $user, 'token' => $token]);
    }


    /**
     * logs a user in
     *
     * @param User $user
     * @return void
     */

    public function adminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $remember_me = $request->has('remember_me') ? true : false;

        if (Auth::attempt(
            [
                'email' => request('email'),
                'password' => request('password')
            ],
            $remember_me
        )) {
            $user = User::where('email', $request->email)->first();

            $token = $user->createToken('token')->accessToken;

            return response()->json(['data' => $user, 'token' => $token]);
        } else {
            return response()->json(['errors' => (object) ['error' => ['Email or password invalid']]], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $user->update([
            'name' => $request->name
        ]);

        return response()->json($user);
    }


    /**
     * logs client out
     */
    public function adminLogout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['status', 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['status' => 'success']);
    }
}
