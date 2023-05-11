<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Resources\UsersResource;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{

    public function __construct()
    {
        // $this->middleware('auth:client');
    }

    function test()
    {
        $bs = Client::all();

        foreach ($bs as $b) {
            $birth = date('Y-m-d H:i:s', strtotime($b->age . ' years ago'));
            Log::info($birth);
            $b->update(['birth_date' => $birth]);
        }
    }
}
