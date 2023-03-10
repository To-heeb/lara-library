<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('library_id')->constrained();
            $table->foreignId('author_id')->constrained();
            $table->foreignId('category_id')->constrained();
            $table->foreignId('publisher_id')->constrained();
            $table->year('published_year');
            $table->string('total_copies');
            $table->string('available_copies');
            $table->string('isbn');
            $table->string('edition');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('books');
    }
}
