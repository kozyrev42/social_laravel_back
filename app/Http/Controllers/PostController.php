<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\CommentRequest;
use App\Http\Requests\Post\StoreRequest;
use App\Http\Resources\Post\CommentResource;
use App\Http\Resources\Post\PostResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\LikedPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function getPostsAuth()
    {
        //
        $posts = Post::where('user_id', auth()->id())
            ->withCount('repostedByPosts') // считаем количество постов, которые были созданы как репост данного оригинального поста
            ->latest()
            ->get();

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

        return PostResource::collection($posts);
    }

    public function createPost(StoreRequest $request)
    {
        // берём данные из запроса, без шляпы и воды
        $data = $request->validated();

        try {
            //  операции с базой данных в блоке "try", будут выполнены как единое целое.
            //  если что-то пойдет не так, все изменения будут откачены
            DB::beginTransaction();

            // Извлекается ID изображения из данных запроса
            $imageId = $data['image_id'];

            //  ID изображения удаляется из данных запроса, так как он больше не требуется
            unset($data['image_id']);

            $data['user_id'] = auth()->id();

            $post = Post::create($data);

            // если предоставлен ID изображения
            if (isset($imageId)) {
                $this->attachImageToPost($post, $imageId);
            }

            PostImage::clearStorage();

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['error' =>$exception->getMessage()]);
        }

        return new PostResource($post);
    }

    public function attachImageToPost($post, $imageId)
    {
        $image = PostImage::find($imageId);

        // к записи выбранного изображения, добавляем ID поста, флаг об активности
        $image->update([
            'active' => true,
            'post_id' => $post->id
        ]);
    }

    /**
     * Метод toggleLike() добавляет или удаляет "лайк" текущего аутентифицированного пользователя к указанному посту.
     * Использует связь likedPosts() для управления отношением "лайка" между пользователем и постом.
     * Возвращает информацию о том, был ли поставлен "лайк" (true) или удален (false).
     *
     * @param  Post  $post  Пост, к которому нужно добавить или удалить "лайк".
     * @return array Массив с ключом 'is_liked', который указывает, был ли поставлен "лайк" (true) или удален (false).
     */
    public function toggleLike(Post $post)
    {
        // Вызываем метод toggle() на связи likedPosts() для добавления или удаления "лайка" к посту
        $res = auth()->user()->likedPosts()->toggle($post->id);

        // Если count($res['attached']) > 0, это означает, что "лайк" был только что поставлен, поэтому is_liked устанавливается в true
        // В противном случае "лайк" был удален, и is_liked устанавливается в false
        $data['is_liked'] = count($res['attached']) > 0;

        // Метод toggle может не сразу обновлять счетчик лайков в связанной модели.
        // Перезагрузить модель Post перед подсчетом лайков:
        $post->refresh();

        // получаем юзеров которые лайкнули, считаем
        $data['likes_count'] = $post->likedUsers->count();

        return $data;
    }

    public function repost(Request $request, Post $post)
    {
        $data = $request->all();

        $data['user_id'] = auth()->id();
        $data['reposted_id'] = $post->id;

        $repost = Post::create($data);
        return $repost;
    }

    public function createComment(CommentRequest $request, Post $post)
    {
        $data = $request->validated();
        $data['post_id'] = $post->id;
        $data['user_id'] = auth()->id();

        $comment = Comment::create($data);

        return new CommentResource($comment);
    }

    public function getComments(Post $post)
    {
        $comments = $post->comments()->get();

        return CommentResource::collection($comments);
    }

}
