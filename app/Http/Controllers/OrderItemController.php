<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderItemController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($orderId)
    {
        $orderItems = OrderItem::query()
            ->where('order_id', '=', $orderId)
            ->orderBy('delivery_time')
            ->get();

        return $this->response
            ->array(['order_items' => $orderItems->toArray()]);
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
    public function store($orderId, Request $request)
    {
        Order::query()
            ->findOrFail($orderId);

        $request->validate([
            'work_item_id' => 'required|exists:work_items',
            'delivery_time' => 'required|datetime',
        ]);

        $inputs = $request->all();
        $inputs['order_id'] = $orderId;

        $orderItem = OrderItem::query()
            ->create($inputs);

        return $this->response
            ->array(['order_item' => $orderItem]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $orderItem = OrderItem::find($id);

        return $this->response
            ->array(['order_item' => $orderItem]);
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
    public function update(Request $request, $id)
    {
        OrderItem::findOrFail($id)
            ->update($request->except(['work_item_id']));

        return $this->response->created();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        OrderItem::findOrFail($id)
            ->delete();

        return $this->response->created();
    }
}
