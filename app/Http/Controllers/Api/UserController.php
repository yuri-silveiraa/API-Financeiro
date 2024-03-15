<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Balance;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUsers()
    {
        return response()->json(UserResource::collection(User::all()), 200);
    }

    public function getUser($id)
    {
        $user = User::findOrFail($id);
        if(! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json(UserResource::collection($user), 200);
    }

    public function createUser(Request $r)
    {
        $created = User::create($r->all());
        if(! $created) {
            return response()->json(['message' => 'Created error'], 400);
        }
        $createSale = Balance::create(['user_id' => $created->id, 'amount' => $r->amount]);

        return response()->json([$created, $createSale], 200);
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
