<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseProduct;
use Dingo\Api\Http\Request;

class PurchaseProductController extends BaseController
{
    public function index(Request $request)
    {
        $request->validate([
            'purchase_id' => 'required|exists:purchases,id'
        ]);

        $purchaseProducts = PurchaseProduct::query()
            ->where('purchase_id', '=', $request->input('purchase_id'))
            ->get();

        return $this->response
            ->array(['purchase_products' => $purchaseProducts->toArray()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'product_id' => 'required|exists:products,id',
            'unit_price' => 'required',
            'quantity' => 'required|numeric',
            'unit' => 'required|string',
        ]);

        $product = Product::query()
            ->where('id', '=', $request->input('product_id'))
            ->firstOrFail();

        PurchaseProduct::query()
            ->create([
                'purchase_id' => $request->input('purchase_id'),
                'product_id' => $request->input('product_id'),
                'name' => $product->name,
                'unit_price' => $request->input('unit_price'),
                'quantity' => $request->input('quantity'),
                'unit' => $request->input('unit'),
                'total_price' => $request->input('unit_price') * $request->input('quantity'),
            ]);

        return $this->response
            ->created();
    }

    public function update(Request $request, $purchaseId)
    {
        $product = Product::query()
            ->where('id', '=', $request->input('product_id'))
            ->firstOrFail();

        $attrs = [
            'name' => $product->name,
            'unit_price' => $request->input('unit_price'),
            'quantity' => $request->input('quantity'),
            'unit' => $request->input('unit'),
            'total_price' => $request->input('unit_price') * $request->input('quantity'),
        ];

        PurchaseProduct::query()
            ->where('id', '=', $purchaseId)
            ->update($attrs);

        return $this->response->created();
    }

    public function batchDelete(Request $request)
    {
        PurchaseProduct::query()
            ->whereIn('id', $request->input('ids'))
            ->delete();

        return $this->response
            ->created();
    }
}
