<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Post\PostResource;
use App\Http\Resources\User\UserResource;
use App\Models\Post;
use App\Models\SubscriberFollowing;
use App\Models\User;

class UserController extends Controller
{
    public function getUsers()
    {
        $users = User::where('id', '!=', auth()->id())->get();

        // получаем ID всех пользователей, на которых подписан текущий аутентифицированный
        // выбирает все строки, где subscriber_id равен ID текущего аутентифицированного
        $followingIds = SubscriberFollowing::where('subscriber_id', auth()->id())

            // извлекаем только значения колонки followings_id из полученных записей.
            // Это оставит вас с коллекцией, состоящей только из значений followings_id.
            ->pluck('followings_id')

            // конвертирует коллекцию в массив
            ->toArray();

        foreach ($users as $user) {
            //  проверяет, находится ли ID текущего пользователя $user в списке followingIds
            if (in_array($user->id, $followingIds)) {
                $user->is_followed = true;
            }
        }

        return UserResource::collection($users);
    }

    public function getPostsUser(User $user)
    {
        // получаем посты, через отношение Модели User
        $posts = $user->posts;
        return PostResource::collection($posts);
    }

    public function toggleFollowing(User $user)
    {
        // auth()->user()->followings() возвращает всех пользователей, на которых подписан текущий аутентифицированный
        // toggle($user->id) прикрепляет или открепляет указанный user_id от текущего аутентифицированного
        // toggle возвращает массив, содержащий два элемента: attached и detached, которые содержат ID пользователей, которые были прикреплены или откреплены
        $res = auth()->user()->followings()->toggle($user->id);

        // если count($res['attached']) > 0, это значит, что пользователь был только что подписан, так что is_followed устанавливается в true
        // иначе пользователь был отписан, и is_followed устанавливается в false
        $data['is_followed'] = count($res['attached']) > 0;
        return $data;
    }

    public function followingPost()
    {
        // получаем ID, на кого подписан аутентифицированный
        $followingIds = auth()->user()->followings()->pluck('followings_id')->toArray();

        $posts = Post::whereIn('user_id', $followingIds)->latest()->get();

        return PostResource::collection($posts);
    }
}
