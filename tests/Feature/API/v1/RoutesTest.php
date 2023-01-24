<?php

namespace Tests\Feature\API\v1;

use Tests\TestCase;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoutesTest extends TestCase
{
    use WithFaker;

    // 1) Define the goal
    // 2) Replicate the environment / restriction
    // 3)Define source of truth
    // 4)Compare the result

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_librarian_can_register_successfully()
    {
        $payload = [
            'first_name' => $this->faker->firstName,
            'last_name'  => $this->faker->lastName,
            'user_name' => $this->faker->lastName,
            'password' => "19491949",
            'password_confirmation' => "19491949",
            'email' => $this->faker->email,
            'library_id' => 1,
        ];

        $this->json('post', '/api/v1/librarian/register', $payload)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(
                [
                    'status',
                    'message',
                    'data'
                ]
            );
    }
}
