<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Using "apiResource" as opposed to "Resource" will only create index, store, show, update, and destroy routes.
Route::apiResource('/users', 'UserController');

});

Route::post('/register', 'UserController@register');
