<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;

class PaymentController extends BaseController
{
    public function getPaymentDetail(Request $request, $orderId)
    {
        $query = OrderItem::with('products')
            ->select([
                'order_items.*',
                'work_items.name as work_item_name'
            ])
            ->leftJoin('work_items', 'work_items.id', '=', 'order_items.work_item_id')
            ->where('order_id', '=', $orderId);

        if ($request->has('is_funeral_offering')) {
            $query->where('is_funeral_offering', '=', $request->input('is_funeral_offering'));
        }

        $orderItems = $query->orderBy('work_items.tracking_number')
            ->get();

        return $this->response
            ->array(['orderItems' => $orderItems->toArray()]);
    }
}
