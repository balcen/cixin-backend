<?php

namespace App\Http\Controllers;

use App\Models\OrderItemProduct;
use App\Models\Product;
use Dingo\Api\Http\Request;

class OrderItemProductController extends BaseController
{
    public function getItemProducts($id)
    {
        $orderItemProducts = OrderItemProduct::query()
            ->where('order_item_id', '=', $id)
            ->get();

        return $this->response
            ->array(['order_item_products' => $orderItemProducts->toArray()]);
    }

    public function update(Request $request, $id)
    {
        $product = Product::query()
            ->where('id', '=', $request->input('product_id'))
            ->firstOrFail();

        $attrs = [
            'name' => $product->name,
            'unit_price' => $request->input('unit_price'),
            'quantity' => $request->input('quantity'),
            'unit' => $request->input('unit'),
            'total_price' => $request->input('unit_price') * $request->input('quantity'),
        ];

        OrderItemProduct::query()
            ->where('id', '=', $id)
            ->update($attrs);

        return $this->response->created();
    }
}
