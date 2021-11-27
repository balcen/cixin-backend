<?php

namespace App\Http\Controllers;

use App\Models\WorkItem;

class WorkItemController extends BaseController
{
    public function index()
    {
        $workItems = WorkItem::all();

        return $this->response
            ->array(['work_items' => $workItems]);
    }
}
