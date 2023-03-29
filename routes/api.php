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
use App\Http\Controllers\BookIssueExtendController;
use App\Http\Controllers\BookIssueReturnController;
use App\Http\Controllers\Admin\UserController as AdminController;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\AuthorController as AdminAuthorController;
use App\Http\Controllers\Admin\LibraryController as AdminLibraryController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\BookIssueController as AdminBookIssueController;
use App\Http\Controllers\Admin\PublisherController as AdminPublisherController;


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


Route::group([
    'prefix' => 'v1',
    'as' => 'api.'
], function () {

    // librarian
    Route::group(['prefix' => 'librarian'], function () {
        Route::post('/register', [AuthController::class, 'librarian_register'])->name('librarian.register');
        Route::post('/libraries', [LibraryController::class, 'store']);
    });

    // admin
    Route::group(['prefix' => 'admin'], function () {
        Route::post('/login', [AuthController::class, 'admin_login'])->name('admin.login');
        Route::post('/register', [AuthController::class, 'admin_register'])->name('admin.register');;
    });


    Route::group([
        'prefix' => 'admin',
        'middleware' =>  ['auth:sanctum', 'role:admin'],
        'as' => 'admin.',
    ], function () {

        // admin
        Route::resource('/authors', AdminAuthorController::class)->only(['index', 'show']);
        Route::resource('/books', AdminBookController::class)->only(['index', 'show']);
        Route::resource('/publishers', AdminPublisherController::class)->only(['index', 'show']);
        Route::resource('/categories', AdminCategoryController::class)->only(['index', 'show']);
        Route::resource('/libraries', AdminLibraryController::class)->only(['index', 'show']);
        Route::resource('/bookissues', AdminBookIssueController::class)->only(['index', 'show']);
        Route::resource('/users', AdminController::class)->only(['index', 'show', 'update']);
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        //Route::delete('/users/{user}', [UserController::class, 'destroy']);
    });
});


Route::domain('{subdomain}.' . config('app.short_url'))->group(function () {

    Route::group([
        'prefix' => 'v1',
        'as' => 'api.'
    ], function () {

        // librarian
        Route::group(['prefix' => 'librarian'], function () {
            Route::post('/login', [AuthController::class, 'librarian_login'])->name('librarian.login');
        });

        // user
        Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
            Route::post('/login', [AuthController::class, 'user_login'])->name('login');
            Route::post('/register', [AuthController::class, 'user_register'])->name('register');
        });

        Route::group(['middleware' => ['auth:sanctum', 'validate_library']], function () {

            // user
            Route::group(
                [
                    'prefix' => 'user',
                    'middleware' => 'role:user',
                    'as' => 'user.',
                ],
                function () {
                    Route::apiResource('/authors', AuthorController::class)->only(['index', 'show']);
                    Route::apiResource('/books', BookController::class)->only(['index', 'show']);
                    Route::apiResource('/publishers', PublisherController::class)->only(['index', 'show']);
                    Route::apiResource('/categories', CategoryController::class)->only(['index', 'show']);
                    Route::get('/libraries/{library}', [LibraryController::class, 'show'])->name('libraries.show');
                    Route::apiResource('/bookissues', BookIssueController::class)->only(['store', 'show']);
                    Route::apiResource('/users', UserController::class)->only(['show', 'update', 'destroy']);
                    Route::name('bookissues.')->group(function () {
                        Route::put('/bookissues/{bookissue}/extend', [BookIssueExtendController::class, 'update'])->name('extend');
                        Route::put('/bookissues/{bookissue}/return', [BookIssueReturnController::class, 'update'])->name('return');
                    });
                }
            );

            // librarian
            Route::group([
                'prefix' => 'librarian',
                'middleware' => 'role:librarian',
                'as' => 'librarian.',
            ], function () {

                Route::apiResources([
                    'authors' => AuthorController::class,
                    'books' => BookController::class,
                    'publishers' => PublisherController::class,
                    'categories' => CategoryController::class,
                    'libraries' => LibraryController::class,
                    'bookissues' => BookIssueController::class,
                ]);

                Route::name('bookissues.')->group(function () {
                    Route::put('/bookissues/{bookissue}/extend', [BookIssueExtendController::class, 'update'])->name('extend');
                    Route::put('/bookissues/{bookissue}/return', [BookIssueReturnController::class, 'update'])->name('return');
                });
                Route::resource('/users', UserController::class)->only(['index', 'update', 'show']);
                //Route::delete('/users/{user}', [UserController::class, 'destroy']);
            });

            // logout
            Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        });
    });
});
