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
    });

    $api->group([
        'prefix' => 'auth',
        'middleware' => 'jwt.auth',
        'namespace' => 'App\Http\Controllers'
    ], function ($api) {
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
        $api->get('customers/{id}', 'CustomerController@show');
        $api->patch('customers/{customer}', 'CustomerController@update');
        $api->delete('customers/{customer}', 'CustomerController@destroy');
        $api->delete('customers', 'CustomerController@batchDelete');
        $api->post('customers/{customer}/bind-product', 'CustomerController@bindProdcut');

        $api->get('customer-info/{customer}', 'CustomerController@getCustomerInfo');

        $api->get('customer-payment/{customer}', 'CustomerController@requestForPayment');

        // Order
        $api->get('orders', 'OrderController@index');
        $api->post('orders', 'OrderController@store');
        $api->get('orders/{order}/order-items', 'OrderController@getItemsWithProducts');
        $api->get('orders/{order}', 'OrderController@show');
        $api->patch('orders/{order}', 'OrderController@update');
        $api->delete('orders/{order}', 'OrderController@destroy');
        $api->delete('orders', 'OrderController@batchDelete');
        $api->get('order/{order}', 'OrderController@getOrderWithCustomerAbbr');

        $api->get('order-info/{order}', 'OrderController@getOrderInfo');

        // Order Item
        $api->get('order-items', 'OrderItemController@index');
        $api->post('order-items', 'OrderItemController@store');
        $api->get('order-items/{orderItem}', 'OrderItemController@show');
        $api->patch('order-items/{orderItem}', 'OrderItemController@update');
        $api->delete('order-items/{orderItem}/order-items', 'OrderItemController@batchDeleteOrderItemProducts');
        $api->delete('order-items/{orderItem}', 'OrderItemController@destroy');
        $api->delete('order-items', 'OrderItemController@batchDelete');
        $api->post('order-items/{orderItem}/product', 'OrderItemController@bindProduct');

        $api->get('daily-shipments', 'OrderItemController@getDailyShipments');
        // 出貨單用 (中一刀)
        $api->get('order-item-stock/{orderItem}', 'OrderItemController@getOrderItemStock');

        $api->get('order-item-info/{orderItem}', 'OrderItemController@getOrderItemInfo');

        // Order Item Product
        $api->patch('order-item-products/{orderItemProduct}', 'OrderItemProductController@update');

        // Work Item
        $api->get('work-items', 'WorkItemController@index');
        $api->post('work-items', 'WorkItemController@store');
        $api->patch('work-items/{workItem}', 'WorkItemController@update');
        $api->delete('work-items', 'WorkItemController@batchDelete');

        // Products
        $api->get('products', 'ProductController@index');
        $api->post('products', 'ProductController@store');
        $api->get('products/{product}', 'ProductController@show');
        $api->patch('products/{product}', 'ProductController@update');
        $api->delete('products', 'ProductController@batchDelete');

        // Product Categories
        $api->get('product-categories', 'ProductCategoryController@index');
        $api->post('product-categories', 'ProductCategoryController@store');
        $api->patch('product-categories/{productCategory}', 'ProductCategoryController@update');
        $api->delete('product-categories', 'ProductCategoryController@batchDelete');

        // Unit
        $api->get('units', 'UnitController@index');
        $api->post('units', 'UnitController@store');
        $api->patch('units/{unit}', 'UnitController@update');
        $api->delete('units', 'UnitController@batchDelete');

        $api->get('funeral-offerings', 'OrderItemController@getFuneralOfferings');

        // Monthly analysis
        $api->get('stock-offering', 'AnalysisController@getStockAndOfferingAmount');

        // 萬安用拜飯請款單
        $api->get('offering-payment', 'AnalysisController@getOfferingPayment');
    });
});
