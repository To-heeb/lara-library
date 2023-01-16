<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Library;
use App\Models\Category;
use App\Models\Publisher;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookIssueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $library_c_ids = User::pluck('library_id')->all();
        $library_c_ids_length = count($library_c_ids) - 1;
        $library_c_id =  $library_c_ids[rand(0, $library_c_ids_length)];
        $library_info = Library::find($library_c_id);
        $max_issue_extentions = $library_info->max_issue_extentions;
        $user_id = User::where('library_id', $library_c_id)->inRandomOrder()->first();
        $now = Carbon::now()->timestamp;
        $addedDays = intval($library_info->book_issue_duration_in_days);
        return [
            //
            'user_id' => $user_id,
            'book_id' => rand(1, 40),
            'library_id' => $library_c_id,
            'issue_date' => $now,
            'due_date' => Carbon::now()->addDays($addedDays)->timestamp,
            'extention_num' => rand(0, $max_issue_extentions),
        ];
    }
}
