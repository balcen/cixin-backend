<?php

namespace App\Http\Controllers;

use App\Models\OrderItemProduct;
use App\Models\Product;
use Dingo\Api\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        DB::beginTransaction();

        $product = Product::query()
            ->where('id', '=', $request->input('product_id'))
            ->firstOrFail();

        try {

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

            DB::commit();

            return $this->response->created();
        } catch (\Exception $e) {
            DB::rollBack();

            $channel = Log::channel('custom');
            $channel->alert($e->getMessage());
            $channel->alert($e->getTraceAsString());

            $this->response->error($e->getMessage(), 500);
        }

    }
}
