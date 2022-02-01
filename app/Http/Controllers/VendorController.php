<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $vendors = Vendor::query()
            ->where('display', '=', $request->input('display', 1))
            ->orderBy('tracking_number')
            ->get();

        return $this->response
            ->array(['vendors' => $vendors->toArray()]);
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

        $vendor = Vendor::query()
            ->create($request->all());

        return $this->response
            ->array(['vendors' => $vendor->toArray()]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $vendor = Vendor::query()
            ->findOrFail($id);

        return $this->response
            ->array(['vendor' => $vendor->toArray()]);
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
        $request->validate([
            'name' => 'required',
            'abbreviation' => 'required'
        ]);

        Vendor::query()
            ->where('id', '=', $id)
            ->update(
                $request->only([
                    "name",
                    "abbreviation",
                    "principal",
                    "contact_person",
                    "tax_number",
                    "invoice_address",
                    "company_address",
                    "company_tel_1",
                    "company_tel_2",
                    "company_tel_3",
                    "company_fax",
                    "company_email",
                    "company_url",
                    "online_order_number",
                    "online_order_password",
                    "payment",
                    "note",
                    "display",
                    "type",
                ])
            );

        return $this->response->created();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vendor $customer)
    {
        $customer->delete();

        return $this->response->created();
    }

    public function batchDelete(Request $request)
    {
        Vendor::query()
            ->whereIn('id', $request->input('ids'))
            ->delete();

        return $this->response->created();
    }
}
