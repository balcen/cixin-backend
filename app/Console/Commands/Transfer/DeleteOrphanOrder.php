<?php

namespace App\Console\Commands\Transfer;

use App\Models\OrderItem;
use App\Models\OrderItemProduct;
use Illuminate\Console\Command;

class DeleteOrphanOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:delete-orphan';

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
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        OrderItem::query()
            ->leftJoin('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.id', '=', null)
            ->delete();

        OrderItemProduct::query()
            ->leftJoin('order_items', 'order_items.id', '=', 'order_item_products.order_item_id')
            ->where('order_items.id', '=', null)
            ->delete();
    }
}
