<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'customer_id' => 'exists:customers.id',
        ]);

        $orderQuery = Order::query();

        if ($request->has('customer_id')) {
            $orderQuery->where('customer_id', '=', $request->input('customer_id'));
        }

        if ($request->exists('date_range')) {
            $orderQuery->whereBetween('date', $request->input('date_range'));
        }
        $orders = $orderQuery->orderBy('date')
            ->get();

        return $this->response
            ->array(['orders' => $orders->toArray()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers',
            'date' => 'required',
            'name' => 'required',
        ]);

        $order = Order::query()
            ->create($request->all());

        return $this->response
            ->array(['order' => $order->toArray()]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        return $this->response
            ->array(['order' => $order->toArray()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        $order->update($request->all());

        return $this->response->created();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        $orderItems = $order->order_items;

        if ($orderItems->count() > 0) {
            $this->response->error('訂單內尚有工作項目', 403);
        }

        $order->delete();

        return $this->response->created();
    }
}
