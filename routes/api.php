<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookIssueController;
use App\Http\Controllers\PublisherController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



Route::group(['prefix' => 'v1'], function () {

    // user
    Route::group(['prefix' => 'user'], function () {
        Route::post('/login', [AuthController::class, 'user_login']);
        Route::post('/register', [AuthController::class, 'user_register']);
    });

    // librrian
    Route::group(['prefix' => 'librarian'], function () {
        Route::post('/login', [AuthController::class, 'librarian_login']);
        Route::post('/register', [AuthController::class, 'librarian_register']);
    });

    // admin
    Route::group(['prefix' => 'admin'], function () {
        Route::post('/login', [AuthController::class, 'admin_login']);
        Route::post('/register', [AuthController::class, 'admin_register']);
    });
});



Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {

    // user
    Route::group(['prefix' => 'user'], function () {
        Route::get('/authors', [AuthorController::class, 'index']);
        Route::get('/authors/{author}', [AuthorController::class, 'show']);
        Route::get('/books', [BookController::class, 'index']);
        Route::get('/books/{book}', [BookController::class, 'show']);
        Route::get('/publishers', [PublisherController::class, 'index']);
        Route::get('/publishers/{publisher}', [PublisherController::class, 'show']);
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::get('/categories/{category}', [CategoryController::class, 'show']);
        Route::get('/libraries', [LibraryController::class, 'index']);
        Route::get('/libraries/{library}', [LibraryController::class, 'show']);
        Route::get('/bookissues', [BookIssueController::class, 'index']);
        Route::get('/bookissues/{bookissue}', [BookIssueController::class, 'show']);
    });

    // librarian
    Route::group(['prefix' => 'librarian'], function () {
        Route::resource('/authors', AuthorController::class);
        Route::resource('/books', BookController::class);
        Route::resource('/publishers', PublisherController::class);
        Route::resource('/categories', CategoryController::class);
        Route::resource('/libraries', LibraryController::class);
        Route::resource('/bookissues', BookIssueController::class);
    });

    // admin
    Route::group(['prefix' => 'admin'], function () {
        Route::get('/authors', [AuthorController::class, 'index']);
        Route::get('/authors/{author}', [AuthorController::class, 'show']);
        Route::get('/books', [BookController::class, 'index']);
        Route::get('/books/{book}', [BookController::class, 'show']);
        Route::get('/publishers', [PublisherController::class, 'index']);
        Route::get('/publishers/{publisher}', [PublisherController::class, 'show']);
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::get('/categories/{category}', [CategoryController::class, 'show']);
        Route::get('/libraries', [LibraryController::class, 'index']);
        Route::get('/libraries/{library}', [LibraryController::class, 'show']);
        Route::get('/bookissues', [BookIssueController::class, 'index']);
        Route::get('/bookissues/{bookissue}', [BookIssueController::class, 'show']);
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{user}', [BookIssueController::class, 'show']);
    });


    Route::post('/logout', [UserController::class, 'logout']);
});
