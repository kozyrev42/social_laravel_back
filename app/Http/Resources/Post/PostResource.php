<?php

namespace App\Http\Resources\Post;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $url = isset($this->image) ? $this->image->url : null;
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'image_url' => $url,
            // будет возвращатся количество времени с момента публикации
            'date' => $this->created_at->diffForHumans(),
            'is_liked' => $this->is_liked ?? false,

            // по отношению из модели, получаем юзеров которые лайкнули, считаем
            'likes_count' => $this->likedUsers->count() ?? false,

            // по отношению получаем оригинальный пост, текущего репоста
            'reposted_post' => new RepostedPostResource($this->repostedPost),

            //
            'reposted_by_posts_count' => $this->reposted_by_posts_count,

            // считаем количество комментариев у поста
            'comments_count' => $this->comments->count(),
        ];
    }
}
