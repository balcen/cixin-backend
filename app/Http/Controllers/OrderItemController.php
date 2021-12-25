<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemProduct;
use App\Models\Product;
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
        ]);

        $query = OrderItem::with('products');

        if ($request->has('order_id')) {
            $query->where('order_id', '=', $request->input('order_id'));
        }

        if ($request->has('date')) {
            $query->whereDate('delivery_time', '=', date($request->input('date')));
        }

        $orderItems = $query->orderBy('delivery_time')
            ->get()
            ->append(['customerAbbr', 'itemName', 'orderName']);

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
            'order_id' => 'required|exists:orders,id',
            'work_item_id' => 'required|exists:work_items,id',
            'delivery_time' => 'required|date',
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
    public function show($orderItemId)
    {
        $orderItem = OrderItem::with('products')
            ->findOrFail($orderItemId);

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
    public function update(Request $request, $id)
    {
        OrderItem::query()
            ->where('id', '=', $id)
            ->update($request->only([
                'work_item_id',
                'delivery_time',
                'deadline',
                'is_funeral_offering',
                'funeral_offering',
                'address',
                'vege_status',
                'note'
            ]));

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

    public function batchDelete(Request $request)
    {
        $hasProducts = OrderItem::query()
            ->whereHas('products')
            ->whereIn('id', $request->input('ids'))
            ->exists();

        if ($hasProducts) {
            $this->response->error('工作項目尚有內容', 400);
        }

        OrderItem::query()
            ->whereIn('id', $request->input('ids'))
            ->delete();

        return $this->response->created();
    }

    public function bindProduct($orderItemId, Request $request)
    {
        $product = Product::query()
            ->findOrFail($request->input('product_id'));

        if ($product->price < 0) {
            $this->response->error('產品未設定價格', 400);
        }

        $orderItem = OrderItem::query()
            ->findOrFail($orderItemId);

        $orderItem->products()
            ->create([
                'name' => $product->name,
                'product_id' => $product->id,
                'unit' => $request->input('unit'),
                'unit_price' => $request->input('unit_price'),
                'total_price' => $request->input('quantity') * $request->input('unit_price'),
                'quantity' => $request->input('quantity')
            ]);

        return $this->response->created();
    }

    public function batchDeleteOrderItemProducts(Request $request, $id)
    {
        OrderItem::query()
            ->findOrFail($id);

        OrderItemProduct::query()
            ->where('order_item_id', '=', $id)
            ->whereIn('id', $request->input('ids'))
            ->delete();

        return $this->response->created();
    }

    public function getDailyShipments(Request $request): \Dingo\Api\Http\Response
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $orderItems = OrderItem::with('products')
            ->where('is_funeral_offering', '=', 0)
            ->whereDate('delivery_time', '=', date($request->input('date')))
            ->orderBy('delivery_time')
            ->get()
            ->append(['customerAbbr', 'itemName', 'orderName']);

        return $this->response
            ->array(['order_items' => $orderItems->toArray()]);
    }
}
