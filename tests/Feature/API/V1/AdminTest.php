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

        $user = User::factory()->create([
            'email' => "example@example.com",
            'role' => 'admin',
            'password' => Hash::make("00000000"),
        ]);

        $this->user = $user;
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

    /**
     * 
     * @test
     */
    public function it_can_login_successfully()
    {
        $data = [
            'email' => $this->user->email,
            'password' => "00000000",
        ];

        $this->postJson(route('api.admin.login'), $data)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(
                [
                    "status",
                    "message",
                    "data" => [
                        "user" =>  [
                            "id",
                            "library_id",
                            "name",
                            "first_name",
                            "last_name",
                            "role",
                            "phone_number",
                            "email",
                            "email_verified_at",
                            "created_at",
                            "updated_at",
                        ],
                        "token"
                    ]
                ]
            );
    }

    /**
     * @test
     */
    public function it_cannot_login_successfully_with_incorrect_creds()
    {
        $data = [
            'email' => $this->user->email,
            'password' => "12345678",
        ];

        $this->postJson(route('api.admin.login'), $data)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_can_fetch_all_admins()
    {
        $author = Author::factory(5)->create(['library_id' => $this->library->id]);

        $this->actingAs($this->user, 'sanctum')
            ->postJson(route('api.admin.authors.index'))
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(
                [
                    "data" => [
                        [
                            'id',
                            "attributes" => [
                                'name',
                                'created_at',
                                'updated_at',
                            ],
                            'relationships' => [
                                'library_id',
                                'library_name',
                                'library_address',
                                'library_email',
                                'library_phone_number',
                                'book_issue_duration_in_days',
                                'max_issue_extentions',
                            ]
                        ]
                    ]

                ]

            );
    }
}
