<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $post = new Post();
        $post->user_id = $request->user()->id;
        $post->content = $request->content;
        $post->save();

        return response()->json($post, 200);
    }

}
