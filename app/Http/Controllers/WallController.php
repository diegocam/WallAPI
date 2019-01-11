<?php

namespace App\Http\Controllers;

use App\Models\User;

class WallController extends Controller
{
    public function index()
    {
        $users = User::with('posts')->has('posts')->get();
        return response()->json($users, 200);
    }

}
