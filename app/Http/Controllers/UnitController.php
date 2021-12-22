<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $units = Unit::query()
            ->orderBy('tracking_number')
            ->get();

        return $this->response
            ->array(['units' => $units->toArray()]);
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
        ]);

        $unit = Unit::query()
            ->create($request->all());

        return $this->response
            ->array(['unit' => $unit->toArray()]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $unit = Unit::find($id);

        return $this->response
            ->array(['unit' => $unit->toArray()]);
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
        Unit::find($id)
            ->update($request->except('tracking_number'));

        return $this->response->created();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Unit::find($id)
            ->delete();

        return $this->response->created();
    }

    public function batchDelete(Request $request)
    {
        $hasProducts = Product::query()
            ->whereIn('unit_id', $request->input('ids'))
            ->exists();

        if ($hasProducts) {
            $this->response->error('單位已經綁定產品', 400);
        }

        Unit::query()
            ->whereIn('id', $request->input('ids'))
            ->delete();

        return $this->response->created();
    }
}
