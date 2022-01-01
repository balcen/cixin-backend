<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $customers = Customer::query()
            ->where('display', '=', $request->input('display', 1))
            ->orderBy('tracking_number')
            ->get();

        return $this->response
            ->array(['customers' => $customers->toArray()]);
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
            'tracking_number' => 'required',
            'name' => 'required',
            'abbreviation' => 'required'
        ]);

        $customer = Customer::query()
            ->create($request->all());

        return $this->response
            ->array(['customer' => $customer->toArray()]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer = Customer::query()
            ->findOrFail($id);

        return $this->response
            ->array(['customer' => $customer->toArray()]);
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
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required',
            'abbreviation' => 'required'
        ]);

        $customer->update(
            $request->except(['tracking_number'])
        );

        return $this->response->created();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return $this->response->created();
    }

    public function batchDelete(Request $request)
    {
        Customer::query()
            ->whereIn('id', $request->input('ids'))
            ->delete();

        return $this->response->created();
    }

    public function requestForPayment(Request $request, $customerId)
    {
        $request->validate([
            'month' => 'required|numeric',
        ]);

        $orderItemSub = DB::table('order_items')
            ->select(
                'order_items.order_id',
                DB::raw('SUM(IFNULL(order_item_products.total_price, 0)) as total_price')
            )
            ->leftJoin('order_item_products', 'order_item_products.order_item_id', '=', 'order_items.id')
            ->groupBy('order_items.order_id');

        $customerPayment = Customer::with(['orders' => function ($query) use ($request, $orderItemSub) {
            $query->select([
                'customer_id',
                'tracking_number',
                DB::raw('IFNULL(sub.total_price, 0) as total_price'),
                'orders.name'
            ])
                ->leftJoinSub($orderItemSub, 'sub', function ($join) {
                    $join->on('sub.order_id', '=', 'orders.id');
                })
                ->whereMonth('orders.end_date', $request->input('month'));
        }])
            ->where('customers.id', '=', $customerId)
            ->first();

        return $this->response->array([
            'customer' => $customerPayment->toArray()
        ]);
    }

    public function getCustomerInfo($id)
    {
        $customer = DB::table('customers')
            ->select(['customers.id', 'customers.abbreviation'])
            ->where('id', '=', $id)
            ->first();

        if (is_null($customer)) {
            return response(['message' => '找不到客戶'], 400);
        }

        return $this->response
            ->array(['customer' => (array) $customer]);
    }
}
