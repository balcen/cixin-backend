<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
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
            'customer_id' => 'exists:customers,id',
        ]);

        $orderQuery = Order::query();

        if ($request->has('customer_id')) {
            $orderQuery->where('customer_id', '=', $request->input('customer_id'));
        }

        if ($request->has('month')) {
            $orderQuery->whereMonth('date', $request->input('month'));
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
            'customer_id' => 'required|exists:customers,id',
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
    public function show($id)
    {
        $order = Order::with(['orderItems' => function ($query) {
            $query->orderBy('delivery_time');
        }])
            ->find($id);

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
    public function update(Request $request, $orderId)
    {
        Order::query()
            ->where('id', '=', $orderId)
            ->update($request->only([
                'name', 'date', 'religion', 'contact_person', 'contact_tel', 'status', 'note'
            ]));

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

    public function batchDelete(Request $request)
    {
        $hasOrderItem = OrderItem::query()
            ->whereIn('order_id', $request->input('ids', []))
            ->exists();

        if ($hasOrderItem) {
            $this->response->error('訂單還有工作項目', 400);
        }

        Order::query()
            ->whereIn('id', $request->input('ids', []))
            ->delete();

        return $this->response->created();
    }

    public function getItemsWithProducts($id)
    {
        $orderItems = OrderItem::with('products')
            ->where('order_id', '=', $id)
            ->get();

        return $this->response
            ->array(['orderItems' => $orderItems->toArray()]);
    }

    public function getOrderWithCustomerAbbr($id)
    {
        $order = Order::query()
            ->select(
                'orders.name',
                'orders.tracking_number',
                'orders.contact_person',
                'orders.contact_tel',
                'orders.religion',
                'customers.tax_number as customer_tax_number',
                'customers.payment as customer_payment',
                'customers.abbreviation as customer_abbreviation',
            )
            ->leftJoin('customers', 'customers.id', '=', 'orders.customer_id')
            ->where('orders.id', '=', $id)
            ->firstOrFail();

        return $this->response
            ->array(['order' => $order->toArray()]);
    }
}
