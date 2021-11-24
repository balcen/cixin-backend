<?php

namespace Database\Factories;


use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{

    public function definition()
    {
        return [
            'customer_id' => function () {
                return Customer::factory()->create()->id;
            },
            'name' => $this->faker->name(),
            'date' => $this->faker->dateTimeBetween('-6 months', '+6 months'),
            'religion' => rand(1, 3),
            'contact_person' => $this->faker->userName(),
            'contact_tel' => $this->faker->phoneNumber(),
            'status' => rand(1, 3),
            'note' => $this->faker->text(),
        ];
    }
}
