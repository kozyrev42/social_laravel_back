<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Post\PostResource;
use App\Http\Resources\User\UserResource;
use App\Models\LikedPost;
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
            //  проверяем, находится ли ID итеративного пользователя $user в списке followingIds
            if (in_array($user->id, $followingIds)) {
                // если есть, условие true, значит аутентифицированный подписан на итеративного $user, устанавливаем флаг
                $user->is_followed = true;
            }
        }

        return UserResource::collection($users);
    }

    public function getPostsUser(User $user)
    {
        // получаем посты, через отношение Модели User
        $posts = $user->posts()
            ->withCount('repostedByPosts') // считаем количество постов, которые были созданы как репост данного оригинального поста
            ->get();

        // полученные посты, отдаём проставить флаг, если был поставлен лайк аутентифицированным
        $posts = $this->prepareLikedPosts($posts);

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
        // pluck('followings_id') извлекает все значения столбца followings_id из результатов запроса, на которых подписан текущий пользователь
        $followingIds = auth()->user()->followings()->pluck('followings_id')->toArray();

        // получаем id постов, которые лайкнул аутентифицированный
        $likedPostIds = LikedPost::where('user_id', auth()->id())
            ->pluck('post_id')
            ->toArray();

        // получаем все посты, где столбец user_id содержит значения, которые находятся в массиве $followingIds
        // посты должны быть не пролайкнуты; если пост пролайкнут, то в выборку результирующего $posts не попадет
        // упорядочивает выбранные записи в обратном порядке по дате создания, самые новые посты будут первыми в результате запроса
        $posts = Post::whereIn('user_id', $followingIds)
            ->whereNotIn('id', $likedPostIds)
            ->withCount('repostedByPosts') // считаем количество постов, которые были созданы как репост данного оригинального поста
            ->latest()
            ->get();

        // полученные посты, отдаём проставить флаг, если был поставлен лайк аутентифицированным
        //$posts = $this->prepareLikedPosts($posts);

        return PostResource::collection($posts);
    }

    // метод проставит флаг, тем постам, которые были пролайкнуты аутентифицированным
    private function prepareLikedPosts($posts)
    {
        $likedPostIds = LikedPost::where('user_id', auth()->id())
            ->pluck('post_id')
            ->toArray();

        foreach ($posts as $post) {
            //  проверяем, находится ли ID итеративного
            if (in_array($post->id, $likedPostIds)) {
                // если есть, условие true, значит
                $post->is_liked = true;
            }
        }

        return $posts;
    }
}
