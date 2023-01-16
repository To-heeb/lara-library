<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LibraryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
            'name' => $this->faker->company,
            'email' => $this->faker->unique()->safeEmail,
            'subdomain' => $this->faker->domainWord,
            'address' => $this->faker->address,
            'phone_number' => $this->faker->phoneNumber,
            'book_issue_duration_in_days' => rand(1, 15),
            'max_issue_extentions' => rand(1, 5),
        ];
    }
}
