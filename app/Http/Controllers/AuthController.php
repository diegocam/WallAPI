<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
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

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
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
                ],
            ], 401);
        }

    }

    private function createToken(Request $request)
    {
        $http = new \GuzzleHttp\Client;
        $response = $http->post(url('/oauth/token'), [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => config('services.passport.client_id'),
                'client_secret' => config('services.passport.client_secret'),
                'username' => $request->input('email'),
                'password' => $request->input('password'),
                'scope' => '',
            ],
        ]);
        return json_decode((string) $response->getBody(), true);
    }
}
