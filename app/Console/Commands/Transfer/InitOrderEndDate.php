<?php

namespace App\Console\Commands\Transfer;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InitOrderEndDate extends Command
{
    protected $orderService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:init-end-date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(OrderService $orderService)
    {
        parent::__construct();

        $this->orderService = $orderService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::beginTransaction();

        try {
            Order::query()
                ->each(function (Order $order) {
                    $endDate = $this->orderService
                        ->getEndedDate($order->id, ['出殯', '結算']);

                    $order->update([
                        'end_date' => $endDate
                    ]);
                });

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }
}
