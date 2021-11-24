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
    public function index(Request $request)
    {
        $request->validate([
            'date' => 'date',
            'date_range' => 'array',
        ]);

        $query = OrderItem::query();

        if ($request->has('order_id')) {
            $query->where('order_id', '=', $request->input('order_id'));
        }

        if ($request->has('date')) {
            $query->whereDate('delivery_time', '=', date($request->input('date')));
        }

        if ($request->has('date_range')) {
            $query->whereBetween('delivery_time', $request->input('date_range'));
        }

        $orderItems = $query->orderBy('delivery_time')
            ->get()
            ->append(['customerAbbr', 'itemName']);

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
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders.id',
            'work_item_id' => 'required|exists:work_items,id',
            'delivery_time' => 'required|datetime',
        ]);

        $orderItem = OrderItem::query()
            ->create($request->all());

        return $this->response
            ->array(['order_item' => $orderItem->toArray()]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(OrderItem $orderItem)
    {
        return $this->response
            ->array(['order_item' => $orderItem->toArray()]);
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
    public function update(Request $request, OrderItem $orderItem)
    {
        $orderItem->update($request->except(['work_item_id']));

        return $this->response->created();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrderItem $orderItem)
    {
        $orderItem->delete();

        return $this->response->created();
    }
}
