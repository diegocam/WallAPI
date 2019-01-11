<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'client_secret' => 'required',
            'client_id' => 'required',
        ]);

        $user = new User([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);
        $user->save();

        $token = $this->createToken($request);

        return response()->json([
            'message' => 'Successfully created user!',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    private function createToken(Request $request)
    {
        $http = new \GuzzleHttp\Client;
        $response = $http->post(url('/oauth/token'), [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => $request->input('client_id'),
                'client_secret' => $request->input('client_secret'),
                'username' => $request->input('email'),
                'password' => $request->input('password'),
                'scope' => '',
            ],
        ]);
        return json_decode((string) $response->getBody(), true);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
            'client_secret' => 'required',
            'client_id' => 'required',
        ]);

        $user = User::query()
            ->where('email', $request->input('email'))
            ->first();

        if ($user && Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'message' => 'Successfully created user!',
                'user' => $user,
                'token' => $this->createToken($request),
            ], 201);
        } else {
            return response()->json([
                'message' => 'Login failed',
                'received' => [
                    $request->input('email'),
                    bcrypt($request->input('password')),
                ]
            ], 401);
        }

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::get();
        return response()->json($user, 200);
    }

    public function getUser(User $user)
    {
        $user = User::with('posts')
            ->where('id', $user->id)
            ->first();
        return response()->json($user, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
