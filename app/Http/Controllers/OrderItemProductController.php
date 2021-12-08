<?php

namespace App\Http\Controllers;

use App\Models\OrderItemProduct;

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
}
