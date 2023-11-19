<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FruitController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostImageController;
use App\Http\Controllers\User\RegistrationController;
use App\Http\Controllers\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// роуты нужно вызывать с префиксом /api/url_роута

Route::get('/test', [Controller::class, 'testApi']);

Route::post('/user/registration', [RegistrationController::class, 'userRegistration']);


// основные маршруты аутентификации
Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class,'logout']);
    Route::post('refresh', [AuthController::class,'refresh']);
    Route::post('me', [AuthController::class,'me']);
});


// роуты которые будут доступны только авторизованным пользователям
Route::group(['middleware' => 'jwt.auth'], function () {
    Route::get('/fruits', [FruitController::class, 'getFruits']);

    Route::get('/posts/auth', [PostController::class, 'getPostsAuth']);
    Route::post('/post/create', [PostController::class,'createPost']);
    Route::post('/post/image', [PostImageController::class,'saveImage']);
    Route::get('/posts/{post}/toggle_like', [PostController::class, 'toggleLike']);
    Route::post('/posts/{post}/repost', [PostController::class, 'repost']);
    Route::post('/posts/{post}/create_comment', [PostController::class, 'createComment']);
    Route::get('/posts/{post}/get_comments', [PostController::class, 'getComments']);


    Route::get('/users', [UserController::class, 'getUsers']);
    Route::get('/users/{user}/posts', [UserController::class, 'getPostsUser']);
    Route::get('/users/{user}/toggle_following', [UserController::class, 'toggleFollowing']);
    Route::get('/users/following_posts', [UserController::class, 'followingPost']);

});
