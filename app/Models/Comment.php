<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = "comments";

    // разрешаем запись во все поля
    protected $guarded = false;

    protected $with = ['user'];

    // обратное отношение, получаем юзера который создал этот пост
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // получение времени создания комментария
    public function getDateAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
