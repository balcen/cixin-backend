<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\OrderItem;
use Dingo\Api\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalysisController extends BaseController
{
    /**
     * @queryParam month optional Default: now
     * @queryParam customer_id required
     * @queryParam work_item_name required
     */
    public function getStockAndOfferingAmount(Request $request)
    {
        $month = $request->input('month', now()->month);

        $productAccountSub = DB::table('order_item_products')
            ->selectRaw('order_item_id, SUM(total_price) as account')
            ->groupBy('order_item_id');

        $customerQuery = Customer::query()
            ->select([
                'customers.id',
                DB::raw("SUM(sub.account) as account")
            ])
            ->leftJoin('orders', 'orders.customer_id', '=', 'customers.id')
            ->leftJoin('order_items', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('work_items', 'work_items.id', '=', 'order_items.work_item_id')
            ->leftJoinSub($productAccountSub, 'sub', 'sub.order_item_id', '=', 'order_items.id')
            ->where('customers.id', '=', $request->input('customer_id'))
            ->groupBy('customers.id');

        if ($request->input('work_item_name') == 'OFFERING') {
            $customerQuery->whereMonth('deadline', '=', $month)
                ->where('work_items.name', 'like', '早/晚拜飯%');
        } else {
            $customerQuery->whereMonth('deliveryTime', '=', $month)
                ->where('work_items.name', 'like', '庫存');
        }

        $customer = $customerQuery->first();

        return $this->response
            ->array(['customer' => $customer->toArray()]);
    }
}
