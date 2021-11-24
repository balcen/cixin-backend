<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Customer::query()
            ->truncate();
        Order::query()
            ->truncate();
        OrderItem::query()
            ->truncate();

        Customer::factory()
            ->count(100)
            ->create()
            ->each(function (Customer $customer) {
                Order::factory()
                    ->count(20)
                    ->create(['customer_id' => $customer->id])
                    ->each(function (Order $order) {
                        OrderItem::factory()
                            ->count(5)
                            ->create(['order_id' => $order->id]);
                    });
            });
    }
}
