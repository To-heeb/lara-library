<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\Book;
use App\Models\User;
use App\Models\Author;
use App\Models\Library;
use App\Models\Category;
use App\Models\BookIssue;
use App\Models\Publisher;
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
    private $library_id;
    private $header;
    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->base_url = "http://lekki.lara-library.test";
        $this->library = Library::factory()->create(['subdomain' => 'lekki', 'book_issue_duration_in_days' => 5]);
        $this->header = array('Content-Type' => 'application/vnd.api+json', "Accept" => 'application/vnd.api+json');
        $this->user = User::factory()->create([
            'role' => 'librarian',
            'password' => Hash::make("00000000"),
            'library_id' => $this->library->id,
        ]);
        $this->library_id = $this->library->id;
    }

    public function test_librarian_can_register_successfully()
    {

        // 1) preparation / prepare
        $payload = [
            'first_name' => $this->faker->firstName,
            'last_name'  => $this->faker->lastName,
            'user_name' => $this->faker->lastName,
            'password' => "00000000",
            'password_confirmation' => "00000000",
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

    public function test_librarian_cannot_login_to_another_library()
    {
        $payload = [
            'password' => "00000000",
            'email' => $this->user->email,
        ];

        $new_library = Library::factory()->create(['subdomain' => 'ikeja']);

        $url = "http://ikeja.lara-library.test/api/v1/librarian/login";

        //dd([$this->user, $new_library]);
        // 2) action / perform
        $response = $this->json('post', $url, $payload, $this->header)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_librarian_can_login_successfully()
    {
        $this->withExceptionHandling();

        // 1) preparation / prepare
        $payload = [
            'password' => "00000000",
            'email' => $this->user->email,
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

        $this->actingAs($this->user, 'sanctum')
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

        $url =  $this->base_url . "/api/v1/librarian/libraries/$library_id";

        //dd($url);
        $this->actingAs($this->user, 'sanctum')
            ->json('put', $url, $payload, $this->header)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_librarian_can_create_an_author()
    {

        $payload = [
            "name" => $this->faker->firstName . " " . $this->faker->lastName,
        ];

        $library_id = $this->library->id;

        $url =  $this->base_url . "/api/v1/librarian/authors";
        //dd([$url]);

        $this->actingAs($this->user, 'sanctum')
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

        $author = Author::factory()->create(['library_id' => $this->library_id]);
        $author_id = $author->id;
        $url =  $this->base_url . "/api/v1/librarian/authors/$author_id";

        $this->actingAs($this->user, 'sanctum')
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

    public function test_librarian_can_fetch_all_author_in_it_library()
    {


        $author = Author::factory(5)->create(['library_id' => $this->library->id]);

        $url =  $this->base_url . "/api/v1/librarian/authors";

        $response = $this->actingAs($this->user, 'sanctum')
            ->json('get', $url, [], $this->header)
            ->assertStatus(Response::HTTP_OK);
        // dd($response);
        $response->assertJsonStructure(
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

    public function test_librarian_can_not_fetch_an_author_not_in_it_library()
    {

        $new_library = Library::factory()->create(['subdomain' => 'ikeja']);
        $library_id = $new_library->id;

        //dd([$user->library_id, $library_id]);
        $author = Author::factory()->create(['library_id' => $library_id]);
        $author_id = $author->id;
        $url =  $this->base_url . "/api/v1/librarian/authors/$author_id";

        //dd($url)
        $this->actingAs($this->user, 'sanctum')
            ->json('get', $url, [], $this->header)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_librarian_can_update_an_author()
    {

        $library_id = $this->library->id;

        $author = Author::factory()->create(['library_id' => $library_id]);
        $author_id = $author->id;

        $payload = [
            "name" => "Oyekola Toheeb",
        ];

        $url =  $this->base_url . "/api/v1/librarian/authors/$author_id";
        //dd([$url]);

        $this->actingAs($this->user, 'sanctum')
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

        $library_id = $this->library->id;

        $author = Author::factory()->create(['library_id' => $library_id]);
        $author_id = $author->id;

        $url =  $this->base_url . "/api/v1/librarian/authors/$author_id";
        //dd([$url]);

        $this->actingAs($this->user, 'sanctum')
            ->json('delete', $url, [], $this->header)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing(Author::class, ['id' => $author_id]);
    }

    public function test_librarian_can_add_a_category()
    {
        $payload = [
            "name" => $this->faker->word,
        ];

        $library_id = $this->library->id;

        $url =  $this->base_url . "/api/v1/librarian/categories";
        //dd([$url]);

        $this->actingAs($this->user, 'sanctum')
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

        $this->assertDatabaseHas(Category::class, ['name' => $payload["name"]]);
    }

    public function test_librarian_can_update_a_category()
    {
        $library_id = $this->library->id;

        $category = Category::factory()->create(['library_id' => $library_id]);
        $category_id = $category->id;

        $payload = [
            "name" => "Fiction",
        ];

        $url =  $this->base_url . "/api/v1/librarian/categories/$category_id";
        //dd([$url, $library_id]);

        $this->actingAs($this->user, 'sanctum')
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

        $this->assertDatabaseHas(Category::class, ['name' => "Fiction"]);
    }

    public function test_librarian_cannot_add_a_category_to_another_library()
    {

        $new_library = Library::factory()->create(['subdomain' => 'ikeja']);

        $payload = [
            "name" => "War",
        ];

        $url =  "http://ikeja.lara-library.test/api/v1/librarian/categories";
        //dd([$url, $library_id, $this->library->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->json('post', $url, $payload, $this->header)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->assertDatabaseMissing(Category::class, ['name' => "War"]);
    }

    public function test_librarian_can_delete_a_category()
    {
        $library_id = $this->library->id;

        $category = Category::factory()->create(['library_id' => $library_id]);
        $category_id = $category->id;

        $url =  $this->base_url . "/api/v1/librarian/categories/$category_id";
        //dd([$url, $library_id]);

        $this->actingAs($this->user, 'sanctum')
            ->json('delete', $url, [], $this->header)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing(Category::class, ['name' =>  $category->name]);
    }

    public function test_librarian_can_fetch_a_category()
    {
        $library_id = $this->library->id;

        $category = Category::factory()->create(['library_id' => $library_id]);
        $category_id = $category->id;

        $url =  $this->base_url . "/api/v1/librarian/categories/$category_id";
        //dd([$url, $library_id]);

        $this->actingAs($this->user, 'sanctum')
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

    public function test_librarian_cannot_fetch_a_category_from_another_library()
    {
        $new_library = Library::factory()->create(['subdomain' => 'ikeja']);

        $library_id = $new_library->id;

        $category = Category::factory()->create(['library_id' => $library_id]);
        $category_id = $category->id;

        $url =  $this->base_url . "/api/v1/librarian/categories/$category_id";
        //dd([$url, $library_id]);

        $this->actingAs($this->user, 'sanctum')
            ->json('get', $url, [], $this->header)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_librarian_can_fetch_all_categories_in_it_library()
    {
        $library_id = $this->library->id;

        $category = Category::factory(5)->create(['library_id' => $library_id]);

        $url =  $this->base_url . "/api/v1/librarian/categories";
        //dd([$url, $library_id]);

        $this->actingAs($this->user, 'sanctum')
            ->json('get', $url, [], $this->header)
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

    public function test_librarian_can_add_a_publisher()
    {

        $payload = [
            "name" => $this->faker->word,
        ];

        $url =  $this->base_url . "/api/v1/librarian/publishers";
        //dd([$url]);

        $this->actingAs($this->user, 'sanctum')
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

        $this->assertDatabaseHas(Publisher::class, ['name' => $payload["name"]]);
    }

    public function test_librarian_can_update_a_publisher()
    {
        $library_id = $this->library->id;

        $publisher = Publisher::factory()->create(['library_id' => $library_id]);
        $publisher_id = $publisher->id;

        $payload = [
            "name" => "Ben Jack Publishing House",
        ];

        $url =  $this->base_url . "/api/v1/librarian/publishers/$publisher_id";
        //dd([$url]);

        $this->actingAs($this->user, 'sanctum')
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

        $this->assertDatabaseHas(Publisher::class, ['name' => $payload["name"]]);
    }

    public function test_librarian_cannot_delete_a_publisher_in_another_library()
    {
        $new_library = Library::factory()->create(['subdomain' => 'ikeja']);
        $library_id = $new_library->id;

        $publisher = Publisher::factory()->create(['library_id' => $library_id]);
        $publisher_id = $publisher->id;

        $url =  $this->base_url . "/api/v1/librarian/publishers/$publisher_id";
        //dd([$url]);

        $this->actingAs($this->user, 'sanctum')
            ->json('delete', $url, [], $this->header)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseHas(Publisher::class, ['name' => $publisher->name]);
    }

    public function test_librarian_can_delete_a_publisher()
    {
        $library_id = $this->library->id;

        $publisher = Publisher::factory()->create(['library_id' => $library_id]);
        $publisher_id = $publisher->id;

        $url =  $this->base_url . "/api/v1/librarian/publishers/$publisher_id";
        //dd([$url]);

        $this->actingAs($this->user, 'sanctum')
            ->json('delete', $url, [], $this->header)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing(Publisher::class, ['name' => $publisher]);
    }

    public function test_librarian_can_fetch_a_publisher()
    {
        $library_id = $this->library->id;

        $publisher = Publisher::factory()->create(['library_id' => $library_id]);
        $publisher_id = $publisher->id;

        $url  = $this->base_url . "/api/v1/librarian/publishers/$publisher_id";

        $this->actingAs($this->user, 'sanctum')
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

    public function test_librarian_can_fetch_all_publishers_in_it_library()
    {
        $library_id = $this->library->id;

        $publisher = Publisher::factory(5)->create(['library_id' => $library_id]);

        $url  = $this->base_url . "/api/v1/librarian/publishers";

        $response = $this->actingAs($this->user, 'sanctum')
            ->json('get', $url, [], $this->header)
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

    public function test_librarian_can_fetch_a_user_from_it_library()
    {
        $library_id = $this->library->id;

        $user = User::factory()->create(['library_id' => $library_id]);
        $user_id = $user->id;

        $url  = $this->base_url . "/api/v1/librarian/users/$user_id";

        $this->actingAs($this->user, 'sanctum')
            ->json('get', $url, [], $this->header)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(
                [
                    "data" => [
                        'id',
                        "attributes" => [
                            'user_name',
                            'first_name',
                            'last_name',
                            'role',
                            'phone_number',
                            'email',
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

    public function test_librarian_can_fetch_all_users_in_it_library()
    {
        $library_id = $this->library->id;

        $user = User::factory(5)->create(['library_id' => $library_id]);

        $url  = $this->base_url . "/api/v1/librarian/users";

        $this->actingAs($this->user, 'sanctum')
            ->json('get', $url, [], $this->header)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(
                [
                    "data" => [
                        [
                            'id',
                            "attributes" => [
                                'user_name',
                                'first_name',
                                'last_name',
                                'role',
                                'phone_number',
                                'email',
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

    public function test_librarian_cannot_fetch_a_users_in_another_library()
    {
        $new_library = Library::factory()->create(['subdomain' => 'ikeja']);
        $library_id = $new_library->id;

        $user = User::factory()->create(['library_id' => $library_id]);
        $user_id = $user->id;

        $url  = $this->base_url . "/api/v1/librarian/users/$user_id";

        $this->actingAs($this->user, 'sanctum')
            ->json('get', $url, [], $this->header)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_librarian_can_add_a_book()
    {

        $library_id = $this->library->id;

        $publisher = Publisher::factory()->create(['library_id' => $library_id]);
        $category = Category::factory()->create(['library_id' => $library_id]);
        $author = Author::factory()->create(['library_id' => $library_id]);
        $payload = [
            "name" => $this->faker->word,
            "publisher_id" => $publisher->id,
            "category_id" => $category->id,
            "author_id" => $author->id,
            "available_copies" => 10,
            "total_copies" => 10,
            "isbn" => $this->faker->phoneNumber,
            "published_year" => $this->faker->year,
            "edition" => '2nd',
        ];

        $url =  $this->base_url . "/api/v1/librarian/books";
        //dd([$url]);

        $this->actingAs($this->user, 'sanctum')
            ->json('post', $url, $payload, $this->header)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(
                [
                    "data" => [
                        'id',
                        "attributes" => [
                            "name",
                            "published_year",
                            "total_copies",
                            "available_copies",
                            "isbn",
                            "edition",
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
                            "author_id",
                            "author_name",
                            "publisher_id",
                            "publisher_name",
                            "category_id",
                            "category_name"
                        ]
                    ]
                ]
            );

        $this->assertDatabaseHas(Book::class, ['name' => $payload["name"]]);
    }

    public function test_librarian_can_update_a_book()
    {
        $library_id = $this->library->id;

        $publisher = Publisher::factory()->create(['library_id' => $library_id]);
        $category = Category::factory()->create(['library_id' => $library_id]);
        $author = Author::factory()->create(['library_id' => $library_id]);
        $book = Book::factory()->create([
            'library_id' => $library_id,
            "publisher_id" => $publisher->id,
            "category_id" => $category->id,
            "author_id" => $author->id,
            "available_copies" => 10,
            "total_copies" => 10,
            "isbn" => $this->faker->phoneNumber,
            "published_year" => $this->faker->year,
            "edition" => '2nd',
        ]);

        $book_id = $book->id;

        $payload = [
            "name" => "Half of a yellow sun",
            'library_id' => $book->library_id,
            "publisher_id" => $book->publisher_id,
            "category_id" => $book->category_id,
            "author_id" => $book->author_id,
            "available_copies" => $book->available_copies,
            "total_copies" => $book->total_copies,
            "isbn" => $book->isbn,
            "published_year" => $book->published_year,
            "edition" => $book->edition,
        ];

        $url =  $this->base_url . "/api/v1/librarian/books/$book_id";
        //dd([$url]);

        $this->actingAs($this->user, 'sanctum')
            ->json('put', $url, $payload, $this->header)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(
                [
                    "data" => [
                        'id',
                        "attributes" => [
                            "name",
                            "published_year",
                            "total_copies",
                            "available_copies",
                            "isbn",
                            "edition",
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
                            "author_id",
                            "author_name",
                            "publisher_id",
                            "publisher_name",
                            "category_id",
                            "category_name"
                        ]
                    ]
                ]
            );

        $this->assertDatabaseHas(Book::class, ['name' => $payload["name"]]);
    }

    public function test_librarian_cannot_update_a_book_not_in_it_library()
    {
        $new_library = Library::factory()->create(['subdomain' => 'ikeja']);
        $library_id = $new_library->id;

        $publisher = Publisher::factory()->create(['library_id' => $library_id]);
        $category = Category::factory()->create(['library_id' => $library_id]);
        $author = Author::factory()->create(['library_id' => $library_id]);
        $book = Book::factory()->create([
            'library_id' => $library_id,
            "publisher_id" => $publisher->id,
            "category_id" => $category->id,
            "author_id" => $author->id,
            "available_copies" => 10,
            "total_copies" => 10,
            "isbn" => $this->faker->phoneNumber,
            "published_year" => $this->faker->year,
            "edition" => '2nd',
        ]);

        $book_id = $book->id;

        $payload = [
            "name" => "Half of a yellow sun",
            'library_id' => $book->library_id,
            "publisher_id" => $book->publisher_id,
            "category_id" => $book->category_id,
            "author_id" => $book->author_id,
            "available_copies" => $book->available_copies,
            "total_copies" => $book->total_copies,
            "isbn" => $book->isbn,
            "published_year" => $book->published_year,
            "edition" => $book->edition,
        ];

        $url =  $this->base_url . "/api/v1/librarian/books/$book_id";
        //dd([$url]);

        $this->actingAs($this->user, 'sanctum')
            ->json('put', $url, $payload, $this->header)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing(Book::class, ['name' => $payload["name"]]);
    }

    public function test_librarian_can_delete_a_book()
    {
        $library_id = $this->library->id;

        $publisher = Publisher::factory()->create(['library_id' => $library_id]);
        $category = Category::factory()->create(['library_id' => $library_id]);
        $author = Author::factory()->create(['library_id' => $library_id]);
        $book = Book::factory()->create([
            'library_id' => $library_id,
            "publisher_id" => $publisher->id,
            "category_id" => $category->id,
            "author_id" => $author->id,
            "available_copies" => 10,
            "total_copies" => 10,
            "isbn" => $this->faker->phoneNumber,
            "published_year" => $this->faker->year,
            "edition" => '2nd',
        ]);

        $book_id = $book->id;

        $url =  $this->base_url . "/api/v1/librarian/books/$book_id";
        //dd([$url]);

        $this->actingAs($this->user, 'sanctum')
            ->json('delete', $url, [], $this->header)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing(Publisher::class, ['name' => $publisher]);
    }

    public function test_librarian_can_fetch_a_book()
    {
        $library_id = $this->library->id;


        $publisher = Publisher::factory()->create(['library_id' => $library_id]);
        $category = Category::factory()->create(['library_id' => $library_id]);
        $author = Author::factory()->create(['library_id' => $library_id]);
        $book = Book::factory()->create([
            'library_id' => $library_id,
            "publisher_id" => $publisher->id,
            "category_id" => $category->id,
            "author_id" => $author->id,
        ]);
        $book_id = $book->id;

        $url  = $this->base_url . "/api/v1/librarian/books/$book_id";

        $this->actingAs($this->user, 'sanctum')
            ->json('get', $url, [], $this->header)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(
                [
                    "data" => [
                        'id',
                        "attributes" => [
                            "name",
                            "published_year",
                            "total_copies",
                            "available_copies",
                            "isbn",
                            "edition",
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
                            "author_id",
                            "author_name",
                            "publisher_id",
                            "publisher_name",
                            "category_id",
                            "category_name"
                        ]
                    ]
                ]
            );
    }

    public function test_librarian_can_fetch_all_books_in_library()
    {
        $library_id = $this->library->id;


        $publisher = Publisher::factory()->create(['library_id' => $library_id]);
        $category = Category::factory()->create(['library_id' => $library_id]);
        $author = Author::factory()->create(['library_id' => $library_id]);
        $book = Book::factory(10)->create([
            'library_id' => $library_id,
            "publisher_id" => $publisher->id,
            "category_id" => $category->id,
            "author_id" => $author->id,
        ]);

        $url  = $this->base_url . "/api/v1/librarian/books";

        $this->actingAs($this->user, 'sanctum')
            ->json('get', $url, [], $this->header)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(
                [
                    "data" => [
                        [
                            'id',
                            "attributes" => [
                                "name",
                                "published_year",
                                "total_copies",
                                "available_copies",
                                "isbn",
                                "edition",
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
                                "author_id",
                                "author_name",
                                "publisher_id",
                                "publisher_name",
                                "category_id",
                                "category_name"
                            ]
                        ]
                    ]
                ]
            );
    }

    public function test_librarian_can_create_a_bookissue()
    {

        $library_id = $this->library->id;

        $publisher = Publisher::factory()->create(['library_id' => $library_id]);
        $category = Category::factory()->create(['library_id' => $library_id]);
        $author = Author::factory()->create(['library_id' => $library_id]);

        $book = Book::factory()->create([
            'library_id' => $library_id,
            "publisher_id" => $publisher->id,
            "category_id" => $category->id,
            "author_id" => $author->id,
            "available_copies" => 10,
            "total_copies" => 10,
            "isbn" => $this->faker->phoneNumber,
            "published_year" => $this->faker->year,
            "edition" => '2nd',
        ]);

        $payload = [
            "user_id" => $this->user->id,
            "return_date" =>  date('Y-m-d', strtotime("2 days")),
            "book_id" => $book->id,
        ];

        $url =  $this->base_url . "/api/v1/librarian/bookissues";
        //dd([$url]);

        $this->actingAs($this->user, 'sanctum')
            ->json('post', $url, $payload, $this->header)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(
                [
                    "data" => [
                        'id',
                        "attributes" => [
                            'issue_date',
                            'return_date',
                            'due_date',
                            'status',
                            'extention_num',
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
                            'book_id',
                            'book_name',
                            'book_author',
                            'book_category',
                            'book_publisher',
                            'total_copies',
                            'available_copies',
                            'published_year',
                            'isbn',
                            'edition',
                        ]
                    ]
                ]
            );

        $this->assertDatabaseHas(Book::class, ['available_copies' => ($book->total_copies - 1)]);
    }

    public function test_librarian_can_extend_a_bookissue()
    {

        $library_id = $this->library->id;

        $publisher = Publisher::factory()->create(['library_id' => $library_id]);
        $category = Category::factory()->create(['library_id' => $library_id]);
        $author = Author::factory()->create(['library_id' => $library_id]);

        $book = Book::factory()->create([
            'library_id' => $library_id,
            "publisher_id" => $publisher->id,
            "category_id" => $category->id,
            "author_id" => $author->id,
            "available_copies" => 10,
            "total_copies" => 10,
            "isbn" => $this->faker->phoneNumber,
            "published_year" => $this->faker->year,
            "edition" => '2nd',
        ]);

        $book_issue = BookIssue::factory()->create([
            'library_id' => $library_id,
            "user_id" => $this->user->id,
            "return_date" =>  date('Y-m-d', strtotime("2 days")),
            "book_id" => $book->id,
        ]);

        $payload = [
            "user_id" => $this->user->id,
            "return_date" =>  date('Y-m-d', strtotime("4 days")),
            "book_id" => $book->id,
        ];

        $url =  $this->base_url . "/api/v1/librarian/bookissues/$book_issue->id/extend";
        //dd([$url]);

        $this->actingAs($this->user, 'sanctum')
            ->json('put', $url, $payload, $this->header)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(
                [
                    "status",
                    "message",
                    "data" => [
                        'id',
                        "attributes" => [
                            'issue_date',
                            'return_date',
                            'due_date',
                            'status',
                            'extention_num',
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
                            'book_id',
                            'book_name',
                            'book_author' => [
                                "id",
                                "library_id",
                                "name",
                                "created_at",
                                "updated_at"
                            ],
                            'book_category' => [
                                "id",
                                "name",
                                "library_id",
                                "created_at",
                                "updated_at"
                            ],
                            'book_publisher' => [
                                "id",
                                "name",
                                "library_id",
                                "created_at",
                                "updated_at"
                            ],
                            'total_copies',
                            'available_copies',
                            'published_year',
                            'isbn',
                            'edition',
                        ]
                    ]
                ]
            );

        $this->assertDatabaseHas(BookIssue::class, ['return_date' => $payload['return_date']]);
    }

    public function test_librarian_cannot_extend_a_bookissue_from_another_library()
    {
        $new_library = Library::factory()->create(['subdomain' => 'ikeja']);
        $library_id = $new_library->id;

        $publisher = Publisher::factory()->create(['library_id' => $library_id]);
        $category = Category::factory()->create(['library_id' => $library_id]);
        $author = Author::factory()->create(['library_id' => $library_id]);

        $book = Book::factory()->create([
            'library_id' => $library_id,
            "publisher_id" => $publisher->id,
            "category_id" => $category->id,
            "author_id" => $author->id,
            "available_copies" => 10,
            "total_copies" => 10,
            "isbn" => $this->faker->phoneNumber,
            "published_year" => $this->faker->year,
            "edition" => '2nd',
        ]);

        $book_issue = BookIssue::factory()->create([
            'library_id' => $library_id,
            "user_id" => $this->user->id,
            "return_date" =>  date('Y-m-d', strtotime("1 days")),
            "book_id" => $book->id,
        ]);

        $payload = [
            "user_id" => $this->user->id,
            "return_date" =>  date('Y-m-d', strtotime("2 days")),
            "book_id" => $book->id,
        ];

        $url =  $this->base_url . "/api/v1/librarian/bookissues/$book_issue->id/extend";

        $this->actingAs($this->user, 'sanctum')
            ->json('put', $url, $payload, $this->header)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_librarian_can_return_a_bookissue()
    {
        $library_id = $this->library->id;

        $publisher = Publisher::factory()->create(['library_id' => $library_id]);
        $category = Category::factory()->create(['library_id' => $library_id]);
        $author = Author::factory()->create(['library_id' => $library_id]);

        $book = Book::factory()->create([
            'library_id' => $library_id,
            "publisher_id" => $publisher->id,
            "category_id" => $category->id,
            "author_id" => $author->id,
            "available_copies" => 10,
            "total_copies" => 10,
            "isbn" => $this->faker->phoneNumber,
            "published_year" => $this->faker->year,
            "edition" => '2nd',
        ]);

        $book_issue = BookIssue::factory()->create([
            'library_id' => $library_id,
            "user_id" => $this->user->id,
            "return_date" =>  date('Y-m-d', strtotime("+1 days")),
            "book_id" => $book->id,
        ]);

        $payload = [
            "user_id" => $this->user->id,
            "book_id" => $book->id,
            "return_date" =>  date('Y-m-d', strtotime("+2 days")),
        ];

        $url =  $this->base_url . "/api/v1/librarian/bookissues/$book_issue->id/return";
        //dd([$url]);

        $this->actingAs($this->user, 'sanctum')
            ->json('put', $url, $payload, $this->header)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(
                [
                    "status",
                    "message",
                    "data" => [
                        'id',
                        "attributes" => [
                            'issue_date',
                            'return_date',
                            'due_date',
                            'status',
                            'extention_num',
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
                            'book_id',
                            'book_name',
                            'book_author' => [
                                "id",
                                "library_id",
                                "name",
                                "created_at",
                                "updated_at"
                            ],
                            'book_category' => [
                                "id",
                                "name",
                                "library_id",
                                "created_at",
                                "updated_at"
                            ],
                            'book_publisher' => [
                                "id",
                                "name",
                                "library_id",
                                "created_at",
                                "updated_at"
                            ],
                            'total_copies',
                            'available_copies',
                            'published_year',
                            'isbn',
                            'edition',
                        ]
                    ]
                ]
            );

        $this->assertDatabaseHas(BookIssue::class, ['status' => 'returned']);
        $this->assertDatabaseHas(Book::class, ['total_copies' => $book['total_copies']]);
    }

    public function test_librarian_can_delete_a_bookissue()
    {
        $library_id = $this->library->id;

        $publisher = Publisher::factory()->create(['library_id' => $library_id]);
        $category = Category::factory()->create(['library_id' => $library_id]);
        $author = Author::factory()->create(['library_id' => $library_id]);

        $book = Book::factory()->create([
            'library_id' => $library_id,
            "publisher_id" => $publisher->id,
            "category_id" => $category->id,
            "author_id" => $author->id,
            "available_copies" => 10,
            "total_copies" => 10,
            "isbn" => $this->faker->phoneNumber,
            "published_year" => $this->faker->year,
            "edition" => '2nd',
        ]);

        $book_issue = BookIssue::factory()->create([
            'library_id' => $library_id,
            "user_id" => $this->user->id,
            "return_date" =>  date('Y-m-d', strtotime("2 days")),
            "book_id" => $book->id,
        ]);

        $url =  $this->base_url . "/api/v1/librarian/bookissues/$book_issue->id";
        //dd([$url]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->json('delete', $url, [], $this->header)
            ->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function test_librarian_can_fetch_a_bookissue()
    {
        $library_id = $this->library->id;

        $publisher = Publisher::factory()->create(['library_id' => $library_id]);
        $category = Category::factory()->create(['library_id' => $library_id]);
        $author = Author::factory()->create(['library_id' => $library_id]);

        $book = Book::factory()->create([
            'library_id' => $library_id,
            "publisher_id" => $publisher->id,
            "category_id" => $category->id,
            "author_id" => $author->id,
            "available_copies" => 10,
            "total_copies" => 10,
            "isbn" => $this->faker->phoneNumber,
            "published_year" => $this->faker->year,
            "edition" => '2nd',
        ]);

        $book_issue = BookIssue::factory()->create([
            'library_id' => $library_id,
            "user_id" => $this->user->id,
            "return_date" =>  "2023-03-05",
            "book_id" => $book->id,
        ]);


        $url =  $this->base_url . "/api/v1/librarian/bookissues/$book_issue->id";
        //dd([$url]);

        $this->actingAs($this->user, 'sanctum')
            ->json('get', $url, [], $this->header)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(
                [
                    "data" => [
                        'id',
                        "attributes" => [
                            'issue_date',
                            'return_date',
                            'due_date',
                            'status',
                            'extention_num',
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
                            'book_id',
                            'book_name',
                            'book_author' => [
                                "id",
                                "library_id",
                                "name",
                                "created_at",
                                "updated_at"
                            ],
                            'book_category' => [
                                "id",
                                "name",
                                "library_id",
                                "created_at",
                                "updated_at"
                            ],
                            'book_publisher' => [
                                "id",
                                "name",
                                "library_id",
                                "created_at",
                                "updated_at"
                            ],
                            'total_copies',
                            'available_copies',
                            'published_year',
                            'isbn',
                            'edition',
                        ]
                    ]
                ]
            );
    }

    public function test_librarian_can_fetch_all_bookissues()
    {
        $library_id = $this->library->id;

        $publisher = Publisher::factory()->create(['library_id' => $library_id]);
        $category = Category::factory()->create(['library_id' => $library_id]);
        $author = Author::factory()->create(['library_id' => $library_id]);

        $book = Book::factory()->create([
            'library_id' => $library_id,
            "publisher_id" => $publisher->id,
            "category_id" => $category->id,
            "author_id" => $author->id,
            "available_copies" => 10,
            "total_copies" => 10,
            "isbn" => $this->faker->phoneNumber,
            "published_year" => $this->faker->year,
            "edition" => '2nd',
        ]);

        $book_issue = BookIssue::factory(5)->create([
            'library_id' => $library_id,
            "user_id" => $this->user->id,
            "return_date" =>  "2023-03-05",
            "book_id" => $book->id,
        ]);


        $url =  $this->base_url . "/api/v1/librarian/bookissues";
        //dd([$url]);

        $this->actingAs($this->user, 'sanctum')
            ->json('get', $url, [], $this->header)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(
                [
                    "data" => [
                        [
                            'id',
                            "attributes" => [
                                'issue_date',
                                'return_date',
                                'due_date',
                                'status',
                                'extention_num',
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
                                'book_id',
                                'book_name',
                                'book_author' => [
                                    "id",
                                    "library_id",
                                    "name",
                                    "created_at",
                                    "updated_at"
                                ],
                                'book_category' => [
                                    "id",
                                    "name",
                                    "library_id",
                                    "created_at",
                                    "updated_at"
                                ],
                                'book_publisher' => [
                                    "id",
                                    "name",
                                    "library_id",
                                    "created_at",
                                    "updated_at"
                                ],
                                'total_copies',
                                'available_copies',
                                'published_year',
                                'isbn',
                                'edition',
                            ]
                        ]
                    ]
                ]
            );
    }

    public function test_books_cannot_be_extended_beyond_library_limit()
    {

        $library_id = $this->library->id;

        $publisher = Publisher::factory()->create(['library_id' => $library_id]);
        $category = Category::factory()->create(['library_id' => $library_id]);
        $author = Author::factory()->create(['library_id' => $library_id]);

        $book = Book::factory()->create([
            'library_id' => $library_id,
            "publisher_id" => $publisher->id,
            "category_id" => $category->id,
            "author_id" => $author->id,
            "available_copies" => 10,
            "total_copies" => 10,
            "isbn" => $this->faker->phoneNumber,
            "published_year" => $this->faker->year,
            "edition" => '2nd',
        ]);

        $book_issue = BookIssue::factory()->create([
            'library_id' => $library_id,
            "user_id" => $this->user->id,
            "return_date" =>  date('Y-m-d', strtotime("2 days")),
            "book_id" => $book->id,
        ]);

        $length = $this->library->max_issue_extentions;

        for ($i = 0; $i <= $length; $i++) {

            $payload = [
                'library_id' => $library_id,
                "user_id" => $this->user->id,
                "return_date" =>  date('Y-m-d', strtotime("+2 days")),
                "book_id" => $book->id,
            ];

            $url =  $this->base_url . "/api/v1/librarian/bookissues/$book_issue->id/extend";

            $this->actingAs($this->user, 'sanctum')
                ->json('put', $url, $payload, $this->header);
        }

        $payload = [
            "user_id" => $this->user->id,
            "return_date" =>  date('Y-m-d', strtotime("+4 days")),
            "book_id" => $book->id,
        ];

        $url =  $this->base_url . "/api/v1/librarian/bookissues/$book_issue->id/extend";
        //dd([$url]);

        $this->actingAs($this->user, 'sanctum')
            ->json('put', $url, $payload, $this->header)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
