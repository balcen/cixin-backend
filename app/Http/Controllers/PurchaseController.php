<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;

class PurchaseController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
        ]);

        $purchaseQuery = Purchase::query();

        if ($request->has('vendor_id')) {
            $purchaseQuery->where('vendor_id', '=', $request->input('vendor_id'));
        }

        if ($request->has('month')) {
            $purchaseQuery->whereMonth('date', $request->input('month'));
        }

        $purchases = $purchaseQuery->orderBy('date')
            ->get();

        return $this->response
            ->array(['purchases' => $purchases->toArray()]);
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
            'vendor_id' => 'required|exists:vendors,id',
            'date' => 'required',
            'name' => 'required',
        ]);

        $purchase = Purchase::query()
            ->create($request->all());

        return $this->response
            ->array(['purchase' => $purchase->toArray()]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $purchase = Purchase::find($id);

        return $this->response
            ->array(['purchase' => $purchase->toArray()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $purchaseId)
    {
        Purchase::query()
            ->where('id', '=', $purchaseId)
            ->update($request->only([
                'name', 'date', 'contact_person', 'contact_tel', 'status', 'note'
            ]));

        return $this->response->created();
    }

    public function batchDelete(Request $request)
    {
//        $hasOrderItem = OrderItem::query()
//            ->whereIn('order_id', $request->input('ids', []))
//            ->exists();
//
//        if ($hasOrderItem) {
//            $this->response->error('訂單還有工作項目', 400);
//        }

        Purchase::query()
            ->whereIn('id', $request->input('ids', []))
            ->delete();

        return $this->response->created();
    }
}
