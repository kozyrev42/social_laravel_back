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
    protected $with = ['image', 'likedUsers', 'repostedPost'];

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

    /**
     * Определяет обратное отношение "один к одному" между текущим постом и оригинальным постом,
     * который был репостнут. Это позволяет получить данные оригинального поста, который
     * этот пост репостит.
     *
     * Каждый пост, который является репостом, ссылается на оригинальный пост через поле 'reposted_id'.
     * Это поле содержит идентификатор оригинального поста в таблице 'posts'. С помощью данного метода
     * можно легко получить доступ к всей информации оригинального поста, включая его заголовок,
     * контент, автора и любые другие связанные данные.
     *
     * Пример использования: $repost->repostedPost для доступа к экземпляру оригинального поста,
     * который был репостнут.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo Возвращает объект отношения BelongsTo,
     * который позволяет использовать методы построения запросов и динамические свойства
     * для работы с отношением и извлечения данных оригинального поста.
     */
    public function repostedPost()
    {
        return $this->belongsTo(Post::class, 'reposted_id', 'id');
    }
}
