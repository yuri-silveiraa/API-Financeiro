<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUsers()
    {
        return response()->json(User::all(), 200);
    }

    public function getUser($id)
    {
        $user = User::findOrFail($id);
        if(! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user, 200);
    }

    public function createUser(Request $r)
    {
        $user = $r->all();
        $created = User::create($user);
        if(! $created) {
            return response()->json(['message' => 'Created error'], 400);
        }
        $createSale = Sale::create()
        return response()->json($created, 200);
    }

    public function updateUser(Request $r, $id)
    {
        $user = User::find($id);
        if(! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->name = $r->name;
        $user->email = $r->email;
        $user->save();

        return response()->json([$user], 200);
    }

    public function deleteUser($id)
    {
        User::destroy($id);

        return response()->json(['message' => 'User deleted'], 200);
    }
}
