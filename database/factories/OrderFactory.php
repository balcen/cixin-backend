<?php

namespace Database\Factories;


use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{

    protected $model = Order::class;

    public function definition()
    {
        return [
            'customer_id' => $this->faker->numberBetween(1, 478),
            'name' => $this->faker->name(),
            'date' => $this->faker->dateTimeBetween('-1 days', '+1 days'),
            'religion' => rand(1, 3),
            'contact_person' => $this->faker->userName(),
            'contact_tel' => $this->faker->phoneNumber(),
            'status' => rand(1, 3),
            'note' => $this->faker->text(),
        ];
    }
}
