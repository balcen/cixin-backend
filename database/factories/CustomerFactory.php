<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'tracking_number' => $this->faker->unique()->regexify('[A-Z][0-9]{5}'),
            'name' => $this->faker->company(),
            'abbreviation' => $this->faker->companySuffix(),
            'principal' => $this->faker->firstNameMale(),
            'contact_person' => $this->faker->firstNameFemale(),
            'tax_number' => $this->faker->phoneNumber(),
            'invoice_address' => $this->faker->address(),
            'company_address' => $this->faker->address(),
            'company_tel_1' => $this->faker->phoneNumber(),
            'company_tel_2' => $this->faker->phoneNumber(),
            'company_tel_3' => $this->faker->phoneNumber(),
            'company_fax' => $this->faker->phoneNumber(),
            'company_email' => $this->faker->companyEmail(),
            'company_url' => $this->faker->url(),
            'online_order_number' => $this->faker->unique()->regexify('[0-9]{10}'),
            'online_order_password' => $this->faker->password(),
            'payment' => $this->faker->boolean() + 1,
            'display' => $this->faker->boolean(),
            'type' => $this->faker->boolean() + 1,
        ];
    }
}
