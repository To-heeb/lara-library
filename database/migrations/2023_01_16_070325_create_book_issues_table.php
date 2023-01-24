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
            $table->foreignId('library_id')->constrained();
            $table->foreignId('book_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->date('issue_date');
            $table->date('return_date');
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
