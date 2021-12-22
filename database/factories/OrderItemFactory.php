<?php

namespace Database\Factories;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    public function definition()
    {
        $date = Carbon::now();

        return [
            'order_id' => Order::factory(),
            'work_item_id' => rand(1, 244),
            'delivery_time' => $this->faker->dateTimeBetween($date, $date->addDays(5)),
            'deadline' => $this->faker->dateTimeBetween($date, $date->addDays(5)),
            'address' => $this->faker->address(),
            'vege_status' => rand(1, 3),
            'note' => $this->faker->text(),
            'status' => rand(1, 3),
        ];
    }
}
