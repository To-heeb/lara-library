<?php

namespace Tests\Feature\API\V1;

use App\Models\Author;
use Tests\TestCase;
use App\Models\User;
use App\Models\Library;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LibrarianTest extends TestCase
{
    use WithFaker, RefreshDatabase;
    // phases of a test
    // 1) preparation / prepare
    // 2) action / perform
    // 3) assertion / predict


    private $base_url;
    private $library;
    private $header;

    public function setUp(): void
    {
        parent::setUp();

        //"http://lekki". config('app.short_url');
        $this->base_url = "http://lekki.lara-library.test";
        $this->library = Library::factory()->create(['subdomain' => 'lekki']);
        $this->header = array('Content-Type' => 'application/vnd.api+json', "Accept" => 'application/vnd.api+json');
    }

    public function test_librarian_can_register_successfully()
    {

        // 1) preparation / prepare
        $payload = [
            'first_name' => $this->faker->firstName,
            'last_name'  => $this->faker->lastName,
            'user_name' => $this->faker->lastName,
            'password' => "19491949",
            'password_confirmation' => "19491949",
            'email' => $this->faker->email,
            'library_id' => 1,
        ];

        // 2) action / perform
        $this->json('post', '/api/v1/librarian/register', $payload)
            ->assertStatus(Response::HTTP_OK)
            // 3) assertion / predict
            ->assertJsonStructure(
                [
                    'status',
                    'message',
                    'data'
                ]
            );
    }


    public function test_librarian_can_create_library_and_login_successfully()
    {

        $this->withExceptionHandling();
        $user = User::factory()->create([
            'role' => 'librarian',
            'password' => Hash::make("19491949"),
            'library_id' => $this->library->id,
        ]);

        // 1) preparation / prepare
        $payload = [
            'password' => "19491949",
            'email' => $user->email,
        ];

        $url =   $this->base_url . "/api/v1/librarian/login";

        // 2) action / perform
        $this->json('post', $url, $payload, $this->header)
            ->assertStatus(Response::HTTP_OK)
            // 3) assertion / predict
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

    public function test_another_role_can_not_create_library_and_login_as_librarian()
    {
        $this->withExceptionHandling();
        $role = ["admin", "user"];
        $user = User::factory()->create([
            'role' => "admin",
            'password' => Hash::make("00000000"),
            'library_id' => $this->library->id,
        ]);

        // 1) preparation / prepare
        $payload = [
            'password' => "00000000",
            'email' => $user->email,
        ];

        $url =  $this->base_url . "/api/v1/librarian/login";

        $this->json('post', $url, $payload, $this->header)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_librarian_can_edit_library_details()
    {
        $this->withExceptionHandling();
        $user = User::factory()->create([
            'role' => 'librarian',
            'password' => Hash::make("19491949"),
            'library_id' => $this->library->id,
        ]);


        $payload = [
            "name" => "Lekki Library",
            "subdomain" => $this->library->subdomain,
            "address" => $this->library->address,
            "email" => $this->library->email,
            "book_issue_duration_in_days" => $this->library->book_issue_duration_in_days,
            "max_issue_extentions" => $this->library->max_issue_extentions,
        ];

        $library_id = $this->library->id;
        $url =  $this->base_url . "/api/v1/librarian/libraries/$library_id";
        //dd([$user, $url]);

        $this->actingAs($user, 'sanctum')
            ->json('put', $url, $payload, $this->header)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(
                [
                    "data" => [
                        "id",
                        "attributes" => [
                            "name",
                            "subdomain",
                            "address",
                            "email",
                            "phone_number",
                            "book_issue_duration_in_days",
                            "max_issue_extentions",
                            "created_at",
                            "updated_at",
                        ]
                    ]
                ]
            );

        $this->assertDatabaseHas(Library::class, ['name' => "Lekki Library"]);
    }

    public function test_librarian_can_not_edit_library_details_of_another_library()
    {
        $user = User::factory()->create([
            'role' => 'librarian',
            'password' => Hash::make("19491949"),
            'library_id' => $this->library->id,
        ]);

        $payload = [
            "name" => "Lekki Library Fake",
            "subdomain" => $this->library->subdomain,
            "address" => $this->library->address,
            "email" => $this->library->email,
            "book_issue_duration_in_days" => $this->library->book_issue_duration_in_days,
            "max_issue_extentions" => $this->library->max_issue_extentions,
        ];

        $new_library = Library::factory()->create(['subdomain' => 'ikeja']);
        $library_id = $new_library->id;
        //dd([$library_id, $this->library->id]);
        $url =  $this->base_url . "/api/v1/librarian/libraries/$library_id";


        //dd($url);
        $this->actingAs($user, 'sanctum')
            ->json('put', $url, $payload, $this->header)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_librarian_can_create_an_author()
    {

        $user = User::factory()->create([
            'role' => 'librarian',
            'password' => Hash::make("19491949"),
            'library_id' => $this->library->id,
        ]);

        $payload = [
            "name" => $this->faker->firstName . " " . $this->faker->lastName,
        ];

        $library_id = $this->library->id;

        $url =  $this->base_url . "/api/v1/librarian/authors";
        //dd([$url]);

        $this->actingAs($user, 'sanctum')
            ->json('post', $url, $payload, $this->header)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(
                [
                    "data" => [
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
            );

        $this->assertDatabaseHas(Author::class, ['name' => $payload["name"]]);
    }

    public function test_librarian_can_fetch_an_author_in_it_library()
    {
        $user = User::factory()->create([
            'role' => 'librarian',
            'password' => Hash::make("19491949"),
            'library_id' => $this->library->id,
        ]);

        $author = Author::factory()->create(['library_id' => $this->library->id]);
        $author_id = $author->id;
        $url =  $this->base_url . "/api/v1/librarian/authors/$author_id";

        $this->actingAs($user, 'sanctum')
            ->json('get', $url, [], $this->header)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(
                [
                    "data" => [
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
            );
    }

    // public function test_librarian_can_fetch_all_author_in_it_library()
    // {

    //     $user = User::factory()->create([
    //         'role' => 'librarian',
    //         'password' => Hash::make("19491949"),
    //         'library_id' => $this->library->id,
    //     ]);

    //     $author = Author::factory(5)->create(['library_id' => $this->library->id]);

    //     $url =  $this->base_url . "/api/v1/librarian/authors";

    //     $response = $this->actingAs($user, 'sanctum')
    //         ->json('get', $url, [], $this->header)
    //         ->assertStatus(Response::HTTP_OK);
    //     // dd($response);
    //     $response->assertJsonStructure(
    //         [
    //             "data" => [
    //                 [
    //                     '*' => [
    //                         "data" => [
    //                             'id',
    //                             "attributes" => [
    //                                 'name',
    //                                 'created_at',
    //                                 'updated_at',
    //                             ],
    //                             'relationships' => [
    //                                 'library_id',
    //                                 'library_name',
    //                                 'library_address',
    //                                 'library_email',
    //                                 'library_phone_number',
    //                                 'book_issue_duration_in_days',
    //                                 'max_issue_extentions',
    //                             ]
    //                         ]
    //                     ]
    //                 ]
    //             ]
    //         ]
    //     );
    // }

    public function test_librarian_can_not_fetch_an_author_not_in_it_library()
    {
        $user = User::factory()->create([
            'role' => 'librarian',
            'password' => Hash::make("19491949"),
            'library_id' => $this->library->id,
        ]);


        $new_library = Library::factory()->create(['subdomain' => 'ikeja']);
        $library_id = $new_library->id;

        //dd([$user->library_id, $library_id]);
        $author = Author::factory()->create(['library_id' => $library_id]);
        $author_id = $author->id;
        $url =  $this->base_url . "/api/v1/librarian/authors/$author_id";

        //dd($url)
        $this->actingAs($user, 'sanctum')
            ->json('get', $url, [], $this->header)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_librarian_can_update_an_author()
    {
        $user = User::factory()->create([
            'role' => 'librarian',
            'password' => Hash::make("19491949"),
            'library_id' => $this->library->id,
        ]);

        $library_id = $this->library->id;

        $author = Author::factory()->create(['library_id' => $library_id]);
        $author_id = $author->id;

        $payload = [
            "name" => "Oyekola Toheeb",
        ];

        $url =  $this->base_url . "/api/v1/librarian/authors/$author_id";
        //dd([$url]);

        $this->actingAs($user, 'sanctum')
            ->json('put', $url, $payload, $this->header)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(
                [
                    "data" => [
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
            );

        $this->assertDatabaseHas(Author::class, ['name' => "Oyekola Toheeb"]);
    }

    public function test_librarian_can_delete_an_author()
    {
        $user = User::factory()->create([
            'role' => 'librarian',
            'password' => Hash::make("19491949"),
            'library_id' => $this->library->id,
        ]);

        $library_id = $this->library->id;

        $author = Author::factory()->create(['library_id' => $library_id]);
        $author_id = $author->id;

        $url =  $this->base_url . "/api/v1/librarian/authors/$author_id";
        //dd([$url]);

        $this->actingAs($user, 'sanctum')
            ->json('delete', $url, [], $this->header)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing(Author::class, ['id' => $author_id]);
    }


    // public function test_librarian_can_add_a_book()
    // {
    // }

    // public function test_librarian_can_update_a_book()
    // {
    // }

    // public function test_librarian_cannot_update_a_book_not_in_it_library()
    // {
    // }

    // public function test_librarian_can_delete_a_book()
    // {
    // }

    // public function test_librarian_can_fetch_a_book()
    // {
    // }

    // public function test_librarian_can_fetch_all_books_in_library()
    // {
    // }

    // public function test_librarian_can_add_a_category()
    // {
    // }

    // public function test_librarian_can_update_a_category()
    // {
    // }

    // public function test_librarian_cannot_add_a_category_to_another_library()
    // {
    // }

    // public function test_librarian_can_delete_a_category()
    // {
    // }

    // public function test_librarian_can_fetch_a_category()
    // {
    // }

    // public function test_librarian_can_fetch_all_categories_in_it_library()
    // {
    // }


    // public function test_librarian_can_add_a_publisher()
    // {
    // }

    // public function test_librarian_can_update_a_publisher()
    // {
    // }

    // public function test_librarian_cannot_delete_a_piblisher_in_another_library()
    // {
    // }

    // public function test_librarian_can_delete_a_publisher()
    // {
    // }

    // public function test_librarian_can_fetch_a_publisher()
    // {
    // }

    // public function test_librarian_can_fetch_all_publishers_in_it_library()
    // {
    // }


    // public function test_librarian_can_fetch_a_user_from_it_library()
    // {
    // }

    // public function test_librarian_can_fetch_all_users_in_it_library()
    // {
    // }

    // public function test_librarian_cannot_fetch_a_users_in_another_library()
    // {
    // }


    // TODOS
    //-book issue for users in the same library
}
