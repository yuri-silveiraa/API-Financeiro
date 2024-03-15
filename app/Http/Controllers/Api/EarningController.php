<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Earning;
use App\Models\User;
use Illuminate\Http\Request;

class EarningController extends Controller
{
    public function getEarning($user_id)
    {
        $earning = Earning::where('user_id', $user_id)->get();

        return response()->json($earning, 200);
    }

    public function createEarning($user_id, Request $r)
    {
        $user = User::Find($user_id);
        if (! $user) {
            return response()->json(['message' => 'Not Found User: '.$user_id], 404);
        }

        $earning = $r->all();

    }

    public function updateEarning()
    {

    }

    public function deleteEarning()
    {

    }
}
