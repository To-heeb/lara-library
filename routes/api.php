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


Route::group(['prefix' => 'v1'], function () {

    // librarian
    Route::group(['prefix' => 'librarian'], function () {
        Route::post('/register', [AuthController::class, 'librarian_register']);
        Route::post('/libraries', [LibraryController::class, 'store']);
    });

    // admin
    Route::group(['prefix' => 'admin'], function () {
        Route::post('/login', [AuthController::class, 'admin_login']);
        Route::post('/register', [AuthController::class, 'admin_register']);
    });


    Route::group(['prefix' => 'admin', 'middleware' => 'auth:sanctum'], function () {

        // admin
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
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::post('/logout', [AuthController::class, 'logout']);
        //Route::delete('/users/{user}', [UserController::class, 'destroy']);
    });
});


Route::domain('{subdomain}.' . config('app.short_url'))->group(function () {

    Route::group(['prefix' => 'v1'], function () {

        // librarian
        Route::group(['prefix' => 'librarian'], function () {
            Route::post('/login', [AuthController::class, 'librarian_login']);
        });

        // user
        Route::group(['prefix' => 'user'], function () {
            Route::post('/login', [AuthController::class, 'user_login']);
            Route::post('/register', [AuthController::class, 'user_register']);
        });

        Route::group(['middleware' => 'auth:sanctum'], function () {

            // user
            Route::group(['prefix' => 'user'], function () {
                Route::resource('/authors', AuthorController::class)->only(['index', 'show']);
                Route::resource('/books', BookController::class)->only(['index', 'show']);
                Route::resource('/publishers', PublisherController::class)->only(['index', 'show']);
                Route::resource('/categories', CategoryController::class)->only(['index', 'show']);
                Route::get('/libraries/{library}', [LibraryController::class, 'show']);
                Route::resource('/bookissues', BookIssueController::class)->only(['store', 'show', 'update']);
                Route::resource('/users/{user}', [UserController::class, 'show'])->only(['show', 'update', 'destroy']);
                Route::delete('/users/{user}', [UserController::class, 'destroy']);
            });

            // librarian
            Route::group(['prefix' => 'librarian'], function () {
                Route::resource('/authors', AuthorController::class);
                Route::resource('/books', BookController::class);
                Route::resource('/publishers', PublisherController::class);
                Route::resource('/categories', CategoryController::class);
                Route::resource('/libraries', LibraryController::class);
                Route::resource('/bookissues', BookIssueController::class);
                Route::get('/users', [UserController::class, 'index']);
                Route::get('/users/{user}', [UserController::class, 'show']);
                Route::put('/users/{user}', [UserController::class, 'update']);
                //Route::delete('/users/{user}', [UserController::class, 'destroy']);
            });

            // logout
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });
});
