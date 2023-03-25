<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->userName,
            'email' => $this->faker->unique()->safeEmail,
            'first_name' => $this->faker->firstNameMale,
            'last_name' => $this->faker->firstNameMale,
            'library_id' => rand(1, 15),
            'phone_number' => $this->faker->phoneNumber,
            'role' => $this->role(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    private function role()
    {
        $index = rand(0, 2);
        $type = ['admin', 'user', 'librarian'];
        return $type[$index];
    }
}
