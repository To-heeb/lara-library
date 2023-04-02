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
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminTest extends TestCase
{
    use WithFaker, RefreshDatabase;


    private $library;
    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->library = Library::factory()->create(['subdomain' => 'lekki', 'book_issue_duration_in_days' => 5]);
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
    public function it_can_fetch_an_author()
    {
        $author = Author::factory()->create(['library_id' => $this->library->id]);
        $author_id = $author->id;

        $this->actingAs($this->user, 'sanctum')
            ->getJson(route('api.admin.authors.show', array('author' => $author_id)))
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

    /**
     * @test
     */
    public function it_can_fetch_all_authors()
    {
        $author = Author::factory(5)->create(['library_id' => $this->library->id]);

        $this->actingAs($this->user, 'sanctum')
            ->getJson(route('api.admin.authors.index'))
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


    /**
     * @test
     */
    public function it_can_fetch_a_book()
    {

        $library_id = $this->library->id;

        $publisher = Publisher::factory()->create(['library_id' => $library_id]);
        $category = Category::factory()->create(['library_id' => $library_id]);
        $author = Author::factory()->create(['library_id' => $library_id]);

        $book = Book::factory()->create([
            'library_id' => $this->library->id,
            "publisher_id" => $publisher->id,
            "category_id" => $category->id,
            "author_id" => $author->id,
        ]);

        $book_id = $book->id;

        $this->actingAs($this->user, 'sanctum')
            ->getJson(route('api.admin.books.show', array('book' => $book_id)))
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

    /**
     * @test
     */
    public function it_can_fetch_all_books()
    {
        $library_id = $this->library->id;

        $publisher = Publisher::factory()->create(['library_id' => $library_id]);
        $category = Category::factory()->create(['library_id' => $library_id]);
        $author = Author::factory()->create(['library_id' => $library_id]);

        $book = Book::factory(5)->create(
            [
                'library_id' => $this->library->id,
                "publisher_id" => $publisher->id,
                "category_id" => $category->id,
                "author_id" => $author->id,
            ]
        );

        $this->actingAs($this->user, 'sanctum')
            ->getJson(route('api.admin.books.index'))
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

    /**
     * @test
     */
    public function it_can_fetch_a_publisher()
    {
        $publisher = Publisher::factory()->create(['library_id' => $this->library->id]);
        $publisher_id = $publisher->id;

        $this->actingAs($this->user, 'sanctum')
            ->getJson(route('api.admin.publishers.show', array('publisher' => $publisher_id)))
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

    /**
     * @test
     */
    public function it_can_fetch_all_publishers()
    {
        $author = Publisher::factory(5)->create(['library_id' => $this->library->id]);

        $this->actingAs($this->user, 'sanctum')
            ->getJson(route('api.admin.publishers.index'))
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

    /**
     * @test
     */
    public function it_can_fetch_a_category()
    {
        $category = Category::factory()->create(['library_id' => $this->library->id]);
        $category_id = $category->id;

        $this->actingAs($this->user, 'sanctum')
            ->getJson(route('api.admin.categories.show', array('category' => $category_id)))
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

    /**
     * @test
     */
    public function it_can_fetch_all_categories()
    {
        $category = Category::factory(5)->create(['library_id' => $this->library->id]);

        $this->actingAs($this->user, 'sanctum')
            ->getJson(route('api.admin.categories.index'))
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

    /**
     * @test
     */
    public function it_can_fetch_a_library()
    {
        $library = Library::factory()->create();
        $library_id = $library->id;

        $this->actingAs($this->user, 'sanctum')
            ->getJson(route('api.admin.libraries.show', array('library' => $library_id)))
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
    }

    /**
     * @test
     */
    public function it_can_fetch_all_libraries()
    {
        $category = Library::factory(5)->create();

        $this->actingAs($this->user, 'sanctum')
            ->getJson(route('api.admin.libraries.index'))
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(
                [
                    "data" => [
                        [
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

                ]

            );
    }

    /**
     * @test
     */
    public function it_can_fetch_a_bookissue()
    {
        $library_id = $this->library->id;

        $publisher = Publisher::factory()->create(['library_id' => $library_id]);
        $category = Category::factory()->create(['library_id' => $library_id]);
        $author = Author::factory()->create(['library_id' => $library_id]);

        $bookissue = BookIssue::factory()->create([
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
        $bookissue_id = $bookissue->id;

        $this->actingAs($this->user, 'sanctum')
            ->getJson(route('api.admin.bookissues.show', array('bookissue' => $bookissue_id)))
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
    }

    /**
     * @test
     */
    public function it_can_fetch_all_bookissues()
    {
        $library_id = $this->library->id;

        $publisher = Publisher::factory()->create(['library_id' => $library_id]);
        $category = Category::factory()->create(['library_id' => $library_id]);
        $author = Author::factory()->create(['library_id' => $library_id]);

        $category = BookIssue::factory(5)->create([
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

        $this->actingAs($this->user, 'sanctum')
            ->getJson(route('api.admin.bookissue.index'))
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

                ]

            );
    }
}
