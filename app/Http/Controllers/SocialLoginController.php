<?php

namespace App\Http\Controllers;

use App\Models\client;
use App\Models\Social;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SocialLoginController extends Controller
{




  public function callback(Request $request, $provider)
  {
    if ($request->provider && $request->token) {

      $getInfo = Socialite::driver($provider)->stateless()->userFromToken($request->token);
      $socialLogin = $this->createUser($getInfo, $provider);
      if ($socialLogin) {
        $token =  $socialLogin->client->createToken('token')->accessToken;
        return response()->json([
          'data' => $socialLogin->client, 'token' => $token,
          'message' => 'User created successfully',
        ], 200);
      } else {
        return response()->json(['error' => (object) ['error' => ['Something went wrong']]], 422);
      }
    } else if (!$request->token) {
      return response()->json(['error' => (object) ['error' => ['Invalid credentials']]], 401);
    } else {
      return response()->json(['error' => (object) ['error' => ['Something went wrong! try again']]], 422);
    }
  }


  function createUser($getInfo, $provider)
  {
    $socialLogin = Social::where('provider_id', $getInfo->id)->first();

    DB::transaction(function () use ($socialLogin, $getInfo, $provider) {
      if (!$socialLogin) {
        $client = Client::create([
          'name'     => $getInfo->name,
          'email'    => $getInfo->email ?? $getInfo->id,
          'provider' => $provider,
          'password' => Hash::make(Str::random(24)),
        ]);

        if ($client) {
          $socialLogin = Social::create([
            'client_id' => $client->id,
            'provider_name' => $provider,
            'provider_id' => $getInfo->id
          ]);
        }
      }
    });
    return $socialLogin;
  }
}
