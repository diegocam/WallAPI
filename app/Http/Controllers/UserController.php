<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Returns a list of all users
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = User::get();
        return response()->json($user, 200);
    }

    /**
     * Returns a single user with proper relations
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser(User $user)
    {
        $user = User::with('posts.comments.user')
            ->where('id', $user->id)
            ->first();
        return response()->json($user, 200);
    }

}
