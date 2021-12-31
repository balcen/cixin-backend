<?php

namespace App\Services;

use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function getEndedDate($orderId, array $endedWorkItemNames)
    {
        $query = DB::table('order_items')
            ->select('order_items.delivery_time', 'work_items.name')
            ->leftJoin('work_items', 'work_items.id', '=', 'order_items.work_item_id')
            ->where('order_items.order_id', '=', $orderId);

        $query->where(function ($query) use ($endedWorkItemNames) {
            foreach ($endedWorkItemNames as $endedWorkItemName) {
                $query->orWhere('work_items.name', 'like', '%' . $endedWorkItemName . '%');
            }
        });


        $orderItem = $query->orderByDesc('delivery_time')
            ->first();

        if (is_null($orderItem)) {
            return null;
        }

        return Carbon::create($orderItem->delivery_time);
    }
}
