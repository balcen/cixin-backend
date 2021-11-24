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

    $api->group([
        'middleware' => 'jwt.auth',
        'namespace' => 'App\Http\Controllers'
    ], function ($api) {
        // Customer
        $api->get('customers', 'CustomerController@index');
        $api->post('customers', 'CustomerController@store');
        $api->get('customers/{customer}', 'CustomerController@show');
        $api->patch('customers/{customer}', 'CustomerController@update');
        $api->delete('customers/{customer}', 'CustomerController@destroy');

        // Order
        $api->get('orders', 'OrderController@index');
        $api->post('orders', 'OrderController@store');
        $api->get('orders/{order}', 'OrderController@show');
        $api->patch('orders/{order}', 'OrderController@update');
        $api->delete('orders/{order}', 'OrderController@destroy');

        // Order Item
        $api->get('order-items', 'OrderItemController@index');
        $api->post('order-items', 'OrderItemController@store');
        $api->get('order-items/{orderItem}', 'OrderItemController@show');
        $api->patch('order-items/{orderItem}', 'OrderItemController@update');
        $api->delete('order-items/{orderItem}', 'OrderItemController@destroy');
    });
});
