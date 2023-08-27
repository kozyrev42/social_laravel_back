<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = "posts";

    // разрешаем запись во все поля
    protected $guarded = false;
}
