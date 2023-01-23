<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RouteTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/api/v1/librarian/register');

        $response->assertStatus(200);
    }

    // 1) Define the goal
    // 2) Replicate the environment / restriction
    // 3)Define source of truth
    // 4)Compare the result
}
