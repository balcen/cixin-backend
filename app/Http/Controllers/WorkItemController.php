<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\WorkItem;
use Dingo\Api\Http\Request;

class WorkItemController extends BaseController
{
    public function index()
    {
        $workItems = WorkItem::query()
            ->orderBy('tracking_number')
            ->get();

        return $this->response
            ->array(['work_items' => $workItems]);
    }

    public function store(Request $request)
    {
        $hasTrackingNumber = WorkItem::query()
            ->where('tracking_number', '=', $request->input('tracking_number'))
            ->exists();

        if ($hasTrackingNumber) {
            $this->response->error('編號已經存在', 400);
        }

        WorkItem::query()
            ->create($request->only('tracking_number', 'name'));

        return $this->response->created();
    }

    public function update(Request $request, $id)
    {
        WorkItem::query()
            ->where('id', '=', $id)
            ->update($request->only('name'));

        return $this->response->created();
    }

    public function batchDelete(Request $request)
    {
        $hasWorkItems = OrderItem::query()
            ->whereIn('work_item_id', $request->input('ids'))
            ->exists();

        if ($hasWorkItems) {
            $this->response->error('訂單已經包含工作項目', 400);
        }

        WorkItem::query()
            ->whereIn('id', $request->input('ids'))
            ->delete();

        return $this->response->created();
    }
}
