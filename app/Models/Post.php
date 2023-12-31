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
    protected $with = ['image', 'likedUsers', 'repostedPost', 'user'];

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

    /**
     * Определяет отношение "один ко многим" между оригинальным постом и постами, которые являются его репостами.
     * Это позволяет получить коллекцию всех постов, которые были созданы как репост данного оригинального поста,
     * и посчитать их количество.
     *
     * Каждый пост, который является репостом другого поста, имеет в поле 'reposted_id' идентификатор
     * оригинального поста. Используя это отношение, можно легко получить все репосты оригинального поста,
     * обращаясь к нему как к родительскому.
     *
     * Пример использования: $originalPost->repostedByPosts для получения коллекции постов,
     * которые являются репостами оригинального поста с идентификатором $originalPost->id.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany Возвращает объект отношения HasMany,
     * который позволяет использовать методы построения запросов и динамические свойства
     * для работы с отношением и извлечения данных о репостах.
     */
    public function repostedByPosts()
    {
        return $this->hasMany(Post::class, 'reposted_id', 'id');
    }

    // получаем много комментариев у одного поста
    // каждый комментарий имеет 'post_id'
    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id', 'id');
    }

    // каждый пост имеет 'user_id', по нему мы и получим юзера который создал пост
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
