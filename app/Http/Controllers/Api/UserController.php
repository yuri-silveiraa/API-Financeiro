<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BalanceResource;
use App\Http\Resources\UserResource;
use App\Models\Balance;
use App\Models\Earning;
use App\Models\Expense;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use HttpResponses;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['update', 'destroy']);
    }

    public function index()
    {
        return $this->response('Suceccfully', 200, UserResource::collection(User::all()));
    }

    public function show($id)
    {
        $user = User::find($id);
        if(! $user) {
            return $this->error( 'User not found', 404);
        }

        return $this->response('Sucessfully', 200, new UserResource($user));
    }

    public function store(Request $r)
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

        $validated = $validator->validated();

        $created = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => password_hash($validated['password'], PASSWORD_DEFAULT),
        ]);
        if(! $created) {
            return $this->error('Created error', 400);
        }
        $createSale = Balance::create(['user_id' => $created->id, 'amount' => $r->amount]);

        return $this->response('Create user sucess', 200, [new UserResource($created), new BalanceResource($createSale)]);
    }

    public function update(Request $r, User $user)
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
            'password' => password_hash($validated['password'], PASSWORD_DEFAULT) ?? $user->password,
        ]);

        return $this->response('Updated Sucess', 200, new UserResource($user));
    }

    public function destroy($id)
    {
        Expense::where('user_id', $id)->detele();
        Earning::where('user_id', $id)->detele();
        Balance::destroy($id);
        $deleted = User::destroy($id);
        if (! $deleted) {
            return $this->error('Failed to delete user', 422);
        }

        return $this->response('Deleted user Sucessfuly', 200);
    }
}
