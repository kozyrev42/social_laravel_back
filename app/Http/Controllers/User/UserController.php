<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Post\PostResource;
use App\Http\Resources\User\UserResource;
use App\Models\Post;
use App\Models\User;

class UserController extends Controller
{
    public function getUsers()
    {
        $users = User::where('id', '!=', auth()->id())->get();
        return UserResource::collection($users);
    }

    public function getPostsUser(User $user)
    {
        // получаем посты, через отношение Модели User
        $posts = $user->posts;
        return PostResource::collection($posts);
    }
}
