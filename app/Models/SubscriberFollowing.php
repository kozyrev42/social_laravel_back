<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriberFollowing extends Model
{
    protected $table = "subscriber_followings";

    // разрешаем запись во все поля
    protected $guarded = false;
}
