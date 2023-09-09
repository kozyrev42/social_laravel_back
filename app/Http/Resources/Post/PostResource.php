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
            'title' => $this->title,
            'content' => $this->content,
            'image_url' => $url,
            // будет возвращатся количество времени с момента публикации
            'date' => $this->created_at->diffForHumans()
        ];
    }
}
