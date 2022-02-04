<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemProduct;
use App\Models\Purchase;
use Carbon\Carbon;
use Dingo\Api\Http\Request;
use Illuminate\Database\Eloquent\Builder;
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
        $request->validate([
            'date' => 'required',
            'customer_id' => 'required',
        ]);

        $date = $request->input('date');
        $year = explode('-', $date)[0];
        $month = explode('-', $date)[1];

        $productAccountSub = DB::table('order_item_products')
            ->selectRaw('order_item_id, SUM(total_price) as account')
            ->groupBy('order_item_id');

        $customerQuery = Customer::query()
            ->select([
                'customers.id',
                DB::raw("SUM(IFNULL(account, 0)) as account"),
            ])
            ->leftJoin('orders', 'orders.customer_id', '=', 'customers.id')
            ->leftJoin('order_items', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('work_items', 'work_items.id', '=', 'order_items.work_item_id')
            ->leftJoinSub($productAccountSub, 'sub', 'sub.order_item_id', '=', 'order_items.id')
            ->where('customers.id', '=', $request->input('customer_id'));

        if ($request->input('work_item_name') == 'OFFERING') {
            $customerQuery->whereYear('order_items.deadline', '=', $year)
                ->whereMonth('order_items.deadline', '=', $month)
                ->where('work_items.name', 'like', '早/晚拜飯%');
        } else {
            $customerQuery->whereYear('order_items.delivery_time', '=', $year)
                ->whereMonth('order_items.delivery_time', '=', $month)
                ->where('work_items.name', 'like', '庫存');
        }

        $customer = $customerQuery
            ->groupBy('customers.id')
            ->get();

        return $this->response
            ->array(['customers' => $customer->toArray()]);
    }

    public function getOfferingPayment(Request $request)
    {
        $month = Carbon::parse($request->input('month'));

        $orderItems = OrderItem::query()
            ->select([
                'orders.tracking_number as order_tracking_number',
                'orders.name',
                'order_items.id',
                'order_items.delivery_time',
                'order_items.deadline',
                'work_items.name as work_item_name',
                'order_item_products.name as order_item_product_name',
                'order_item_products.unit_price as order_item_product_unit_price',
            ])
            ->leftJoin('order_item_products', 'order_item_products.order_item_id', '=', 'order_items.id')
            ->leftJoin('orders', 'orders.id', '=', 'order_items.order_id')
            ->leftJoin('work_items', 'work_items.id', '=', 'order_items.work_item_id')
            ->where('is_funeral_offering', '=', 1)
            ->whereYear('deadline', '=', $month->year)
            ->whereMonth('deadline', '=', $month->month)
            ->where('orders.customer_id', '=', $request->input('customer_id'))
            ->get();

        return $this->response
            ->array([
                'order_items' => $orderItems
            ]);
    }

    public function getIncoming(Request $request)
    {
        if ($request->has('month')) {
            $month = Carbon::parse($request->input('month'));
        } else {
            $month = Carbon::now();
        }

        $analysis = Order::query()
            ->select([
                DB::raw("SUM(order_item_products.total_price) as total_price")
            ])
            ->leftJoin(
                'order_items',
                'order_items.order_id',
                '=',
                'orders.id'
            )
            ->leftJoin(
                'order_item_products',
                'order_item_products.order_item_id',
                '=',
                'order_items.id'
            )
            ->where(function (Builder $query) use ($month) {
                $query->whereYear('end_date', '=', $month)
                    ->whereMonth('end_date', '=', $month);
            })
            ->first();

        return $this->response
            ->array(['analysis' => $analysis->getAttribute('total_price') ?? 0]);
    }

    public function getOutgoing(Request $request)
    {
        if ($request->has('month')) {
            $month = Carbon::parse($request->input('month'));
        } else {
            $month = Carbon::now();
        }

        $analysis = Purchase::query()
            ->select([
                DB::raw("SUM(purchase_products.total_price) as total_price")
            ])
            ->leftJoin('purchase_products', 'purchase_products.purchase_id', '=', 'purchases.id')
            ->where(function (Builder $query) use ($month) {
                $query->whereYear('date', '=', $month)
                    ->whereMonth('date', '=', $month);
            })
            ->first();

        return $this->response
            ->array(['analysis' => $analysis->getAttribute('total_price') ?? 0]);
    }
}
