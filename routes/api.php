<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\CategoryController;
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
        Route::post('/login', [UserController::class, 'librarian_login']);
    });

    // librrian
    Route::group(['prefix' => 'librarian'], function () {
        Route::post('/login', [UserController::class, 'librarian_login']);
    });

    // admin
    Route::group(['prefix' => 'admin'], function () {
        Route::post('/login', [UserController::class, 'admin_login']);
    });


    Route::post('/logout', [UserController::class, 'logout']);
});



Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {

    // user
    Route::group(['prefix' => 'user'], function () {
        Route::post('/login', [UserController::class, 'librarian_login']);
    });

    // librrian
    Route::group(['prefix' => 'librarian'], function () {
        Route::resource('/authors', AuthorController::class);
        Route::resource('/books', BookController::class);
        Route::resource('/publishers', PublisherController::class);
        Route::resource('/categories', CategoryController::class);
        Route::resource('/library', LibraryController::class);
    });

    // admin
    Route::group(['prefix' => 'admin'], function () {
        Route::post('/login', [UserController::class, 'admin_login']);
    });
});
