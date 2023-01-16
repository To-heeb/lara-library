<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Library;
use App\Models\Publisher;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $library_c_ids = Category::pluck('library_id')->all();
        $library_c_ids_length = count($library_c_ids) - 1;
        $library_c_id =  $library_c_ids[rand(0, $library_c_ids_length)];
        $library_p_ids = Publisher::pluck('library_id')->all();
        $library_p_ids_length = count($library_p_ids) - 1;
        $library_p_id =  $library_p_ids[rand(0, $library_p_ids_length)];
        $category_id = Category::where('library_id', $library_c_id)->inRandomOrder()->first();
        $publisher_id = Publisher::where('library_id', $library_p_id)->inRandomOrder()->first();

        // if (!is_null($category_id)) {
        // }
        // echo '<pre>';
        // var_dump($category_id);
        // echo '</pre>';
        return [
            //
            'name' => $this->faker->company,
            'library_id' => $library_c_id,
            'category_id' => $category_id,
            'publisher_id' => $publisher_id,
            'published_year' => $this->faker->year,
            'isbn' => $this->faker->isbn13,
            'edition' => rand(4, 15) . "th Edition",
        ];
    }
}
