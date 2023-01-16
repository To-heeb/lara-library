<?php

namespace Database\Seeders;

use App\Models\BookIssue;
use Illuminate\Database\Seeder;

class BookIssueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        BookIssue::factory(10)->create();
    }
}
