<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

        $json = file_get_contents(storage_path('customers.json'));
        $customers = json_decode($json, true);

        foreach($customers as $customer) {
            if (empty($customer['name'])) continue;

            $customer = Customer::query()
                ->create([
                    'tracking_number' => $customer['tracking_number'],
                    'name' => $customer['name'],
                    'abbreviation' => $customer['name'],
                    'company_tel_1' => $customer['number'],
                ]);

//            Order::factory()
//                ->for($customer)
//                ->has(
//                    OrderItem::factory()
//                        ->count(20)
//                )
//                ->count(10)
//                ->create();
        }
    }
}
