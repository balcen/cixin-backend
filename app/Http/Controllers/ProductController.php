<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $productsQuery = Product::query()
            ->select(['products.*'])
            ->leftJoin(
                'product_categories',
                'product_categories.id',
                '=',
                'products.product_category_id'
            );

        if ($request->has('product_category_id')) {
            if ($request->input('product_category_id') !== 'all') {
                $productsQuery->where('product_category_id', '=', $request->input('product_category_id'));
            }
        } else if ($request->exists('product_category_ids')) {
            $productsQuery->whereIn('product_category_id', $request->input('product_category_ids'));
        }

        $products = $productsQuery->where('product_categories.type', '=', 'outgoing')
            ->orderBy('tracking_number')
            ->get()
            ->append('product_category_tracking_number');

        return $this->response
            ->array(['products' => $products]);
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
            'product_category_id' => 'required',
            'name' => 'required',
            'unit_id' => 'required',
        ]);

        $product = Product::query()
            ->create($request->all());

        return $this->response
            ->array(['product' => $product->toArray()]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);

        return $this->response
            ->array(['product' => $product->toArray()]);
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
        Product::find($id)
            ->update($request->only([
                'tracking_number',
                'name',
                'product_category_id',
                'unit_id',
                'price',
                'safety_stock',
                'spec',
                'is_comb',
                'note',
            ]));

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
        Product::find($id)
            ->delete();

        return $this->response->created();
    }

    public function batchDelete(Request $request)
    {
        Product::query()
            ->whereIn('id', $request->input('ids'))
            ->delete();

        return $this->response->created();
    }
}
