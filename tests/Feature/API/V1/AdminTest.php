<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\User;
use App\Models\Library;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class AdminTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    private $base_url;
    private $library;
    private $header;
    private $user;

    public function setUp(): void
    {
        parent::setUp();

        // $user = User::factory()->create([
        //     'role' => 'admin',
        //     'password' => Hash::make("00000000"),
        // ]);

        // Sanctum::actingAs($user, []);
    }

    /**
     *
     * @test
     */
    public function it_can_register()
    {

        $data = [
            'first_name' => $this->faker->firstName,
            'last_name'  => $this->faker->lastName,
            'user_name' => $this->faker->lastName,
            'password' => "00000000",
            'password_confirmation' => "00000000",
            'email' => $this->faker->email,
        ];

        $this->postJson(route('api.admin.register'), $data)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(
                [
                    'status',
                    'message',
                    'data'
                ]
            );

        $this->assertDatabaseHas(User::class, ['last_name' => $data['last_name']]);
    }
}
