<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $productCategories = ProductCategory::query()
            ->orderBy('tracking_number')
            ->get();

        return $this->response
            ->array(['product_categories' => $productCategories->toArray()]);
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

        $hasTrackingNumber = ProductCategory::query()
            ->where('tracking_number', '=', $request->input('tracking_number'))
            ->exists();

        if ($hasTrackingNumber) {
            $this->response->error('編號已經存在', 400);
        }

        $productCategory = ProductCategory::query()
            ->create($request->all());

        return $this->response
            ->array(['product_category' => $productCategory->toArray()]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $productCategory = ProductCategory::find($id);

        return $this->response
            ->array(['product_category' => $productCategory->toArray()]);
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
        $hasTrackingNumber = ProductCategory::query()
            ->where('id', '!=', $id)
            ->where('tracking_number', '=', $request->input('tracking_number'))
            ->exists();

        if ($hasTrackingNumber) {
            $this->response->error('編號已經存在', 400);
        }

        ProductCategory::find($id)
            ->update($request->only('tracking_number', 'name'));

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
        ProductCategory::find($id)
            ->delete();

        return $this->response->created();
    }

    public function batchDelete(Request $request)
    {
        $hasProducts = Product::query()
            ->whereIn('product_category_id', $request->input('ids'))
            ->exists();

        if ($hasProducts) {
            $this->response->error('類別已經綁定產品', 400);
        }

        ProductCategory::query()
            ->whereIn('id', $request->input('ids'))
            ->delete();

        return $this->response->created();
    }

    public function getOutgoing()
    {
        $productCategories = ProductCategory::query()
            ->where('type', '=', 'outgoing')
            ->orderBy('tracking_number')
            ->get();

        return $this->response
            ->array(['product_categories' => $productCategories->toArray()]);
    }

    public function getIncoming()
    {
        $productCategories = ProductCategory::query()
            ->where('type', '=', 'incoming')
            ->orderBy('tracking_number')
            ->get();

        return $this->response
            ->array(['product_categories' => $productCategories->toArray()]);
    }

    public function getCategoryProducts($id)
    {
        $products = Product::query()
            ->where('product_category_id', '=', $id)
            ->orderBy('tracking_number')
            ->get();

        return $this->response
            ->array(['products' => $products]);
    }
}
