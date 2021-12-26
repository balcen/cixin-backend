<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Log;

trait LogHelper
{
    public function customLog(Exception $e)
    {
        $channel = Log::channel('custom');
        $channel->alert($e->getMessage());
        $channel->alert($e->getTraceAsString());
    }
}
