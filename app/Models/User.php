<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // метод JWTSubject
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     * Получите идентификатор, который будет храниться в утверждении субъекта JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    // метод JWTSubject
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     * Возвращает массив значений ключа, содержащий любые настраиваемые утверждения, которые необходимо добавить в JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    // можно получить все посты объекта User вызвав: $user->posts
    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id', 'id')->latest();
    }

    // метод followings() возвращает коллекцию пользователей, на которых подписан данный пользователь
    // связь между моделью User и ее самой, через промежуточную таблицу 'subscriber_followings'
    // 'subscriber_id' - кто подписан/ это подписчик
    // 'followings_id' - на кого подписан Подписчик
    public function followings()
    {
        return $this->belongsToMany(User::class, 'subscriber_followings', 'subscriber_id', 'followings_id');
    }
}
