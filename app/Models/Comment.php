<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = "comments";

    // разрешаем запись во все поля
    protected $guarded = false;
}
