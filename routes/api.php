<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

$api = app('Dingo\Api\Routing\Router');


$api->version('v1', function ($api) {
    $api->group([
        'prefix' => 'auth',
        'namespace' => 'App\Http\Controllers'
    ], function ($api) {
        $api->post('login', 'JwtAuthController@login');
        $api->post('logout', 'JwtAuthController@logout');
        $api->post('me', 'JwtAuthController@me');
    });
});
