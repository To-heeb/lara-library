<?php

namespace Tests\Feature\API\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @test
     */
    public function example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
