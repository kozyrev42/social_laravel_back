<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = "posts";

    // разрешаем запись во все поля
    protected $guarded = false;

    // вызов метода image() при обращении к модели Post
    // при загрузке модели Post, связанная модель PostImage (то есть изображение)
    // будет автоматически загружена вместе с ней. Это называется "жадной загрузкой" (eager loading)
    // и служит для оптимизации количества запросов к базе данных.
    protected $with = ['image'];

    // определяет отношение к модели PostImage
    public function image()
    {
        // каждый Post может иметь только одно изображение PostImage
        return $this->hasOne(PostImage::class, 'post_id', 'id');
    }

    /**
     * отношение позволяет получить коллекцию пользователей, которые "лайкнули" данный пост.
     * Используется промежуточная таблица 'liked_posts', где 'post_id' и 'user_id'
     * связывают записи в таблицах 'posts' и 'users' соответственно.
     * 
     * Пример использования: $post->likedUsers для получения коллекции пользователей,
     * которые лайкнули пост с идентификатором $post->id.
     */
    public function likedUsers()
    {
        return $this->belongsToMany(User::class, 'liked_posts', 'post_id', 'user_id');
    }
}
