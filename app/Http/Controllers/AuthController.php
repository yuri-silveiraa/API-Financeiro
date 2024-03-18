<?php

namespace App\Http\Controllers;

use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use HttpResponses;

    // 2|PrklJcExSEoIDEU4Xtm3DaK2lQMbJiyWMauja5GR40d440f3
    public function login(Request $r)
    {
        if(Auth::attempt($r->only(['email', 'password']))) {
            return $this->response('authorized', 200, [
                'token' => $r->user()->createToken('all', ['all-index', 'all-create', 'all-update', 'all-delete'])->plainTextToken,
            ]);
        }

        return $this->response('Invalid data', 403);

    }

    public function logout()
    {

    }
}
