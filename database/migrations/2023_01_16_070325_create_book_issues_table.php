<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_issues', function (Blueprint $table) {
            $table->id();
            $table->integer('library_id');
            $table->integer('book_id');
            $table->integer('user_id');
            $table->date('issue_date');
            $table->date('return_date')->nullable();
            $table->date('due_date');
            $table->enum('status', ['pending', 'returned'])->default("pending");
            $table->string('extention_num')->default('0');
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
        Schema::dropIfExists('book_issues');
    }
}
