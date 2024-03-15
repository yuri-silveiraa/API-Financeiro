<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BalanceResource;
use App\Http\Resources\UserResource;
use App\Models\Balance;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use HttpResponses;

    public function getUsers()
    {
        return $this->response('Suceccfully', 200, UserResource::collection(User::all()));
    }

    public function getUser($id)
    {
        $user = User::find($id);
        if(! $user) {
            return $this->error( 'User not found', 404);
        }

        return $this->response('Sucessfully', 200, new UserResource($user));
    }

    public function createUser(Request $r)
    {
        $validator = Validator::make($r->all(), [
            'name' => 'required|max:25',
            'email' => 'required|email',
            'password' => 'required|max:25',
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->error('Data Invalid', 422, $validator->errors());
        }

        $created = User::create($validator->validated());
        if(! $created) {
            return $this->error('Created error', 400);
        }
        $createSale = Balance::create(['user_id' => $created->id, 'amount' => $r->amount]);

        return $this->response('Create user sucess', 200, [new UserResource($created), new BalanceResource($createSale)]);
    }

    public function updateUser(Request $r, User $user)
    {
        if(! $user) {
            return $this->error( 'User not found', 404);
        }

        $validator = Validator::make($r->all(), [
            'name' => 'required|max:25',
            'email' => 'required|email',
            'password' => 'nullable',
        ]);

        if ($validator->fails()) {
            return $this->error('Data Invalid', 422, $validator->errors());
        }

        $validated = $validator->validated();

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'] ?? $user->password,
        ]);

        return response()->json([new UserResource($user)], 200);
    }

    public function deleteUser($id)
    {
        User::destroy($id);

        return response()->json(['message' => 'User deleted'], 200);
    }
}
