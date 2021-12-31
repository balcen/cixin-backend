<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemProduct;
use App\Models\Product;
use App\Models\WorkItem;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderItemController extends BaseController
{
    const END_WORK_ITEM = ['出殯', '結帳'];

    use LogHelper;

    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

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

        $endDate = $this->orderService
            ->getEndedDate($request->input('order_id'), self::END_WORK_ITEM);

        Order::query()
            ->where('id', '=', $request->input('order_id'))
            ->update([
                'end_date' => $endDate
            ]);

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
        $orderItem = OrderItem::query()
            ->findOrFail($id);

        $orderItem->update($request->only([
            'work_item_id',
            'delivery_time',
            'deadline',
            'is_funeral_offering',
            'funeral_offering',
            'address',
            'vege_status',
            'note'
        ]));

        $endDate = $this->orderService
            ->getEndedDate($request->input('order_id'), self::END_WORK_ITEM);

        Order::query()
            ->where('id', '=', $request->input('order_id'))
            ->update([
                'end_date' => $endDate
            ]);

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
        DB::beginTransaction();

        try {
            $hasProducts = OrderItemProduct::query()
                ->whereIn('order_item_id', $request->input('ids', []))
                ->exists();

            if ($hasProducts) {
                $this->response->error('工作項目尚有內容', 400);
            }

            $orderItemQuery = OrderItem::query()
                ->whereIn('id', $request->input('ids'));

            $orderItems = $orderItemQuery->get();

            $orderItemQuery->delete();

            $orderItems->pluck('order_id')
                ->unique()
                ->each(function ($orderId) {
                     $endDate = $this->orderService
                         ->getEndedDate($orderId, self::END_WORK_ITEM);

                     Order::query()
                         ->where('id', '=', $orderId)
                         ->update([
                             'end_date' => $endDate
                         ]);
                });

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            $this->customLog($e);

            $this->response->error($e->getMessage(), 403);
        }

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

    protected function hasEndTarget($workItemId)
    {
        $endTargets = ['出殯', '結帳'];

        return WorkItem::query()
            ->where('id', '=', $workItemId)
            ->whereIn('name', $endTargets)
            ->exists();
    }

    public function getFuneralOfferings(Request $request)
    {
        $request->validate([
            'date' => 'date',
            'is_day' => 'boolean',
        ]);

        $date = Carbon::create($request->input('date'));
        if ($request->input('is_day')) {
            $date->setTime(9, 0);
        } else {
            $date->setTime(15, 0);
        }

        $orderItemQuery = OrderItem::query()
            ->where('is_funeral_offering', '=', 1);

        if ($request->has('funeral_offering') && $request->input('funeral_offering')) {
            $orderItemQuery->where('funeral_offering', '=', $request->input('funeral_offering'));
        } else {
            $orderItemQuery->where(function (Builder $query) {
                $query->where('funeral_offering', '=', 0)
                    ->orWhereNull('funeral_offering');
            });
        }

        $orderItems = $orderItemQuery->where(function (Builder $query) use ($date, $request) {
                $query->whereDate('delivery_time', '<', $date)
                    ->where(function ($query) use ($date) {
                        $query->whereDate('deadline', '>', $date)
                            ->orWhereNull('deadline');
                    });

                if ($request->input('is_day')) {
                    $query->orWhere(function ($query) use ($date) {
                        $query->whereDate('delivery_time', '=', $date)
                            ->whereTime('delivery_time', '<', Carbon::parse('12:00'));
                    })
                        ->orWhereDate('deadline', '=', $date)
                        ->orWhere(function (Builder $query) use ($date) {
                            $query->whereDate('deadline', $date->subDay())
                                 ->whereTime('deadline', '>', Carbon::parse('12:00'));
                        });
                } else {
                    $query->orWhereDate('delivery_time', '=', $date)
                        ->orWhereDate('deadline', $date);
                }
            })
            ->get()
            ->append(['orderName', 'customerAbbr']);

        return $this->response->array([
            'order_items' => $orderItems->toArray(),
        ]);
    }
}
