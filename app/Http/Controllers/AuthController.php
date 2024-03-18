<?php

namespace App\Http\Controllers;

use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use HttpResponses;

    public function login(Request $req)
    {
        if(Auth::attempt($req->only(['email', 'password']))) {
            return $this->response('authorized', 200, [
                'token' => $req->user()->createToken('all', ['all-index', 'all-create', 'all-update', 'all-delete'])->plainTextToken,
            ]);
        }

        return $this->response('Invalid data', 403);

    }

    public function logout(Request $req)
    {
        $req->user()->currentAccessToken()->delete();
    }
}
