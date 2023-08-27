<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostImage extends Model
{
    protected $table = "post_images";

    // разрешаем запись во все поля
    protected $guarded = false;
}
